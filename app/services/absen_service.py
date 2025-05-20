from sqlalchemy.orm import Session
from app.models.absen import Absen
from app.models.user import User
from app.models.absen_session import AbsenSession
from app.schemas.absen import AbsenCreate
from typing import List
from fastapi import HTTPException

def create_absen(db: Session, absen_data: AbsenCreate) -> Absen:
    # Get mahasiswa by nrp
    mahasiswa = db.query(User).filter(User.nrp == absen_data.users_nrp, User.role == "mahasiswa").first()

    if not mahasiswa:
        raise HTTPException(status_code=404, detail="Mahasiswa not found.")
    
    # Check if absen already exists for the given jadwal
    existing_absen = db.query(Absen).filter(
        Absen.id_jadwal == absen_data.id_jadwal,
        Absen.id_mahasiswa == mahasiswa.nrp
    ).first()

    if existing_absen:
        raise HTTPException(status_code=400, detail="Absen already exists for this jadwal.")
    
    # Create new absen record
    absen_record = Absen(
        id_jadwal=absen_data.id_jadwal,
        id_mahasiswa=mahasiswa.nrp,
        status=absen_data.status
    )

    db.add(absen_record)
    db.commit()
    db.refresh(absen_record)
    return absen_record

def get_all_absen(db: Session) -> List[Absen]:
    return db.query(Absen).all()

def get_absen_by_id(db: Session, id_absen: int) -> Absen:
    return db.query(Absen).filter(Absen.id_absen == id_absen).first()

def get_absen_by_mahasiswa(db: Session, id_mahasiswa: int) -> List[Absen]:
    return db.query(Absen).filter(Absen.id_mahasiswa == id_mahasiswa).all()

def update_absen(db: Session, id_absen: int, absen_data: AbsenCreate) -> Absen:
    absen_record = get_absen_by_id(db, id_absen)
    if not absen_record:
        raise HTTPException(status_code=404, detail="Absen record not found.")
    
    absen_record.status = absen_data.status  # Asumsi hanya status yang dapat diubah
    db.commit()
    db.refresh(absen_record)
    return absen_record

def delete_absen(db: Session, id_absen: int) -> bool:
    absen_record = get_absen_by_id(db, id_absen)
    if not absen_record:
        return False
    db.delete(absen_record)
    db.commit()
    return True
