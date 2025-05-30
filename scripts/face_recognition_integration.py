import os
import io
import cv2
import torch
import time
import logging
import numpy as np
from PIL import Image
from collections import defaultdict
from torchvision import transforms
from facenet_pytorch import MTCNN, InceptionResnetV1

from app.config import SessionLocal
from app.schemas.absen import AbsenCreate
from app.services import absen_service
from app.models.user import User

logging.basicConfig(level=logging.INFO)
device = torch.device(
    "cuda" if torch.cuda.is_available()
    else "mps" if torch.backends.mps.is_available()
    else "cpu"
)
logging.info(f"[Face Recognition] Using device: {device}")

mtcnn = MTCNN(keep_all=True, device=device)
face_encoder = InceptionResnetV1(pretrained="vggface2").eval().to(device)
transform = transforms.Compose([
    transforms.Resize((160, 160)),
    transforms.ToTensor()
])

# Cache: {id_jadwal: {nrp: last_seen_time}}
last_seen_faces = defaultdict(dict)
ABSEN_TIMEOUT_SECONDS = 60

def get_face_embeddings(image: np.ndarray) -> list:
    try:
        image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        pil_image = Image.fromarray(image_rgb)
        face_boxes, _ = mtcnn.detect(pil_image)

        results = []
        if face_boxes is not None:
            for box in face_boxes:
                x1, y1, x2, y2 = map(int, box)
                face = pil_image.crop((x1, y1, x2, y2))
                face_tensor = transform(face).unsqueeze(0).to(device)
                with torch.no_grad():
                    embedding = face_encoder(face_tensor).cpu().numpy().flatten()
                results.append((embedding, (x1, y1, x2, y2)))
        return results
    except Exception as e:
        logging.error(f"[Embedding Error] {e}")
        return []

def load_dataset(dataset_path: str) -> tuple:
    encodings, names = [], []
    for person in os.listdir(dataset_path):
        person_dir = os.path.join(dataset_path, person)
        if not os.path.isdir(person_dir):
            continue

        for img_file in os.listdir(person_dir):
            if not img_file.lower().endswith(('.jpg', '.jpeg', '.png')):
                continue

            img_path = os.path.join(person_dir, img_file)
            image = cv2.imread(img_path)
            if image is None:
                continue

            faces = get_face_embeddings(image)
            for embedding, _ in faces:
                encodings.append(embedding)
                names.append(person)

    return np.array(encodings), np.array(names)

def get_name_to_nrp_mapping() -> dict:
    mapping = {}
    db = SessionLocal()
    try:
        mahasiswa_list = db.query(User).filter(User.role == "mahasiswa").all()
        for mhs in mahasiswa_list:
            mapping[mhs.name] = mhs.nrp
    except Exception as e:
        logging.error(f"[NRP Mapping Error] {e}")
    finally:
        db.close()
    return mapping

def recognize_and_absen_from_bytes(
    image_bytes: bytes,
    id_jadwal: int,
) -> dict:
    THRESHOLD = 0.7

    try:
        pil_image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    except Exception as e:
        return {
            "success": False,
            "message": f"Gagal membaca gambar: {str(e)}"
        }

    frame = cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
    face_data = get_face_embeddings(frame)

    if not face_data:
        return {
            "success": False,
            "message": "Tidak ada wajah terdeteksi."
        }

    known_encodings, known_names = load_dataset("scripts/dataset")
    name_to_nrp = get_name_to_nrp_mapping()

    for embedding, _ in face_data:
        distances = np.linalg.norm(known_encodings - embedding, axis=1)
        best_match_index = np.argmin(distances)

        if distances[best_match_index] < THRESHOLD:
            matched_name = known_names[best_match_index]
            nrp = name_to_nrp.get(matched_name)

            if nrp:
                now = time.time()
                if nrp in last_seen_faces[id_jadwal]:
                    last_seen = last_seen_faces[id_jadwal][nrp]
                    if now - last_seen < ABSEN_TIMEOUT_SECONDS:
                        return {
                            "success": True,
                            "nrp": nrp,
                            "status": "hadir",
                            "message": f"{matched_name} ({nrp}) sudah terdeteksi sebelumnya, diabaikan."
                        }

                db = SessionLocal()
                try:
                    absen_data = AbsenCreate(
                        users_nrp=nrp,
                        id_jadwal=id_jadwal,
                        status="hadir"
                    )
                    absen_service.create_absen(db, absen_data)
                    last_seen_faces[id_jadwal][nrp] = now
                    return {
                        "success": True,
                        "nrp": nrp,
                        "status": "hadir",
                        "message": f"Absensi berhasil untuk {matched_name} ({nrp})"
                    }
                except Exception as e:
                    return {
                        "success": False,
                        "message": f"Database error: {str(e)}"
                    }
                finally:
                    db.close()

    return {
        "success": False,
        "message": "Wajah dikenali, tetapi tidak ditemukan NRP-nya."
    }
