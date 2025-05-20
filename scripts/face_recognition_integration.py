import os
import io
import cv2
import base64
import torch
import logging
import numpy as np
from PIL import Image
from sqlalchemy.orm import Session
from torchvision import transforms
from facenet_pytorch import MTCNN, InceptionResnetV1

from app.config import SessionLocal
from app.schemas.absen import AbsenCreate
from app.services import absen_service
from app.models.user import User

# Logging
logging.basicConfig(level=logging.INFO)

# Device setup
device = torch.device("cuda" if torch.cuda.is_available() else ("mps" if torch.backends.mps.is_available() else "cpu"))
logging.info(f"Using device: {device}")

# Models
mtcnn = MTCNN(keep_all=True, device=device)
face_encoder = InceptionResnetV1(pretrained='vggface2').eval().to(device)

# Transform
transform = transforms.Compose([
    transforms.Resize((160, 160)),
    transforms.ToTensor()
])

# Decode base64 string ke image (OpenCV)
def decode_base64_to_frame(base64_str: str) -> np.ndarray:
    try:
        image_data = base64.b64decode(base64_str)
        np_arr = np.frombuffer(image_data, np.uint8)
        frame = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
        return frame
    except Exception as e:
        logging.error(f"Base64 decoding failed: {e}")
        return None

# Ekstraksi embedding wajah dari gambar
def get_face_embeddings(image):
    try:
        image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        pil_image = Image.fromarray(image_rgb)
        face_locations, _ = mtcnn.detect(pil_image)
        results = []
        if face_locations is not None:
            for box in face_locations:
                x1, y1, x2, y2 = map(int, box)
                face = pil_image.crop((x1, y1, x2, y2))
                face_tensor = transform(face).unsqueeze(0).to(device)
                with torch.no_grad():
                    embedding = face_encoder(face_tensor).cpu().numpy().flatten()
                results.append((embedding, (x1, y1, x2, y2)))
        return results
    except Exception as e:
        logging.error(f"Embedding error: {e}")
        return []

# Load dataset embedding wajah
def load_dataset(dataset_path):
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

# Ambil mapping nama → NRP dari database
def get_name_to_nrp_mapping():
    mapping = {}
    db = SessionLocal()
    try:
        mahasiswa = db.query(User).filter(User.role == "mahasiswa").all()
        for mhs in mahasiswa:
            mapping[mhs.name] = mhs.nrp
    except Exception as e:
        logging.error(f"Gagal ambil data NRP: {e}")
    finally:
        db.close()
    return mapping

# Fungsi utama (dipanggil dalam route FastAPI)
def recognize_and_absen_from_bytes(image_bytes: bytes, id_jadwal: int, id_matkul: int, id_session: int):
    THRESHOLD = 0.7

    try:
        pil_image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    except Exception as e:
        return {"status": "error", "message": f"Gagal membaca gambar: {str(e)}"}

    frame = cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
    face_data = get_face_embeddings(frame)
    if not face_data:
        return {"status": "error", "message": "Tidak ada wajah terdeteksi."}

    known_encodings, known_names = load_dataset("scripts/dataset")
    name_to_nrp = get_name_to_nrp_mapping()

    for embedding, _ in face_data:
        distances = np.linalg.norm(known_encodings - embedding, axis=1)
        best_match_index = np.argmin(distances)

        if distances[best_match_index] < THRESHOLD:
            matched_name = known_names[best_match_index]
            nrp = name_to_nrp.get(matched_name)
            if nrp:
                db = SessionLocal()
                try:
                    absen_data = AbsenCreate(
                        users_nrp=nrp,
                        id_jadwal=id_jadwal,
                        status="hadir",
                        detection_time=None,
                    )
                    absen_service.create_absen(db, absen_data)
                    return {"success": True, "nrp": nrp, "message": f"Absensi berhasil untuk {matched_name} ({nrp})"}
                except Exception as e:
                    return {"success": False, "message": f"Database error: {str(e)}"}
                finally:
                    db.close()

    return {"success": False, "message": "Wajah dikenali, tetapi tidak ditemukan NRP-nya."}
