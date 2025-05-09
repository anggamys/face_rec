from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from typing import List

from app.schemas.absen import AbsenCreate, AbsenResponse
from app.models.absen import Absen
from app.config import get_db
from app.services import absen_service  # Pastikan kamu sudah buat service di path ini

router = APIRouter(
    prefix="/absen",
    tags=["Absen"]
)

@router.post("/", response_model=AbsenResponse)
def create_absensi(absen_data: AbsenCreate, db: Session = Depends(get_db)):
    return absen_service.create_absen(db, absen_data)

@router.get("/", response_model=List[AbsenResponse])
def get_all_absen(db: Session = Depends(get_db)):
    return absen_service.get_all_absen(db)

@router.get("/{id_absen}", response_model=AbsenResponse)
def get_absen_by_id(id_absen: int, db: Session = Depends(get_db)):
    absen = absen_service.get_absen_by_id(db, id_absen)
    if not absen:
        raise HTTPException(status_code=404, detail="Absen not found.")
    return absen

@router.get("/mahasiswa/{id_mahasiswa}", response_model=List[AbsenResponse])
def get_absen_by_mahasiswa(id_mahasiswa: int, db: Session = Depends(get_db)):
    return absen_service.get_absen_by_mahasiswa(db, id_mahasiswa)

@router.put("/{id_absen}", response_model=AbsenResponse)
def update_absen(id_absen: int, absen_data: AbsenCreate, db: Session = Depends(get_db)):
    return absen_service.update_absen(db, id_absen, absen_data)

@router.delete("/{id_absen}")
def delete_absen(id_absen: int, db: Session = Depends(get_db)):
    success = absen_service.delete_absen(db, id_absen)
    if not success:
        raise HTTPException(status_code=404, detail="Absen not found.")
    return {"message": "Absen deleted successfully."}
