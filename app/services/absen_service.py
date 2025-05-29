from typing import List
from fastapi import HTTPException
from sqlalchemy.orm import Session

from app.models.absen import Absen
from app.models.user import User
from app.schemas.absen import AbsenCreate

def create_absen(db: Session, absen_data: AbsenCreate) -> Absen:
    """Membuat entri absen untuk mahasiswa pada jadwal tertentu."""
    mahasiswa = db.query(User).filter(
        User.nrp == absen_data.users_nrp,
        User.role == "mahasiswa"
    ).first()

    if not mahasiswa:
        raise HTTPException(status_code=404, detail="Mahasiswa not found.")

    existing_absen = db.query(Absen).filter(
        Absen.id_jadwal == absen_data.id_jadwal,
        Absen.id_mahasiswa == mahasiswa.nrp
    ).first()

    if existing_absen:
        raise HTTPException(status_code=400, detail="Absen already exists for this jadwal.")

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
    """Mengambil semua data absen."""
    return db.query(Absen).all()

def get_absen_by_id(db: Session, id_absen: int) -> Absen:
    """Mengambil data absen berdasarkan ID."""
    return db.query(Absen).filter(Absen.id_absen == id_absen).first()

def get_absen_by_mahasiswa(db: Session, id_mahasiswa: int) -> List[Absen]:
    """Mengambil daftar absen berdasarkan NRP mahasiswa."""
    return db.query(Absen).filter(Absen.id_mahasiswa == id_mahasiswa).all()

def update_absen(db: Session, id_absen: int, absen_data: AbsenCreate) -> Absen:
    """Memperbarui status absen berdasarkan ID absen."""
    absen_record = get_absen_by_id(db, id_absen)
    if not absen_record:
        raise HTTPException(status_code=404, detail="Absen record not found.")

    absen_record.status = absen_data.status
    db.commit()
    db.refresh(absen_record)
    return absen_record

def delete_absen(db: Session, id_absen: int) -> bool:
    """Menghapus data absen berdasarkan ID."""
    absen_record = get_absen_by_id(db, id_absen)
    if not absen_record:
        return False

    db.delete(absen_record)
    db.commit()
    return True
