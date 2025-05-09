from fastapi import APIRouter, Depends, HTTPException, status, BackgroundTasks, Path, Query
from sqlalchemy.orm import Session
from typing import List
from app.schemas.absen import AbsenCreate, AbsenResponse
from app.services import absen_service
from app.config import get_db, get_current_user
from app.models.user import User
from scripts import face_recognition_integration
import threading

router = APIRouter(
    prefix='/absensi',
    tags=["Absensi"]
)

face_recognition_stop_events = {}

# ✅ Buka sesi absensi
@router.post("/session/{id_jadwal}/open", status_code=status.HTTP_200_OK)
def open_absen_session(
    background_tasks: BackgroundTasks,
    id_jadwal: int,
    id_matkul: int = Query(...),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Hanya dosen yang dapat membuka sesi absensi")

    absen_service.open_session(id_jadwal)

    stop_event = threading.Event()
    face_recognition_stop_events[id_jadwal] = stop_event

    background_tasks.add_task(face_recognition_integration.run_face_recognition, id_jadwal, id_matkul, current_user, stop_event)

    return {"detail": f"Sesi absensi untuk id_jadwal {id_jadwal} telah dibuka dan face recognition dijalankan."}

# ✅ Tutup sesi absensi
@router.post("/session/{id_jadwal}/close", status_code=status.HTTP_200_OK)
def close_absen_session(
    id_jadwal: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Hanya dosen yang dapat menutup sesi absensi")

    absen_service.close_session(id_jadwal)

    stop_event = face_recognition_stop_events.get(id_jadwal)
    if stop_event:
        stop_event.set()
        del face_recognition_stop_events[id_jadwal]

    return {"detail": f"Sesi absensi untuk id_jadwal {id_jadwal} telah ditutup."}

# ✅ Tambah data absensi
@router.post("", response_model=AbsenResponse, status_code=status.HTTP_201_CREATED)
def create_absen(
    absen: AbsenCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    if current_user.role == "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Dosen tidak bisa melakukan absensi.")
    if not absen_service.is_session_open(absen.id_jadwal):
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Sesi absensi tidak aktif untuk jadwal ini.")

    return absen_service.create_absen(db, absen)

# ✅ Ambil semua absensi
@router.get("/", response_model=List[AbsenResponse])
def read_all_absen(
    db: Session = Depends(get_db)
):
    return absen_service.get_all_absen(db)
