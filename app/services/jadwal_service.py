from sqlalchemy.orm import Session
from app.models.jadwal import Jadwal
from app.schemas.jadwal import JadwalCreate, JadwalUpdate
from typing import List, Optional
from fastapi import HTTPException, status

# Utility helper
def get_jadwal_or_404(db: Session, id_jadwal: int) -> Jadwal:
    jadwal = db.query(Jadwal).filter(Jadwal.id_jadwal == id_jadwal).first()
    if not jadwal:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Jadwal not found")
    return jadwal

def create_jadwal(db: Session, jadwal: JadwalCreate) -> Jadwal:
    new_jadwal = Jadwal(
        kode_kelas=jadwal.kode_kelas,
        id_matkul=jadwal.id_matkul,
        week=jadwal.week,
        tanggal=jadwal.tanggal
    )
    db.add(new_jadwal)
    db.commit()
    db.refresh(new_jadwal)
    return new_jadwal

def get_all_jadwal(db: Session) -> List[Jadwal]:
    return db.query(Jadwal).all()

def get_jadwal_by_id(db: Session, id_jadwal: int) -> Jadwal:
    return get_jadwal_or_404(db, id_jadwal)

def get_jadwal_by_kelas(db: Session, kode_kelas: str) -> List[Jadwal]:
    return db.query(Jadwal).filter(Jadwal.kode_kelas == kode_kelas).all()

def update_jadwal(db: Session, id_jadwal: int, jadwal_update: JadwalUpdate) -> Jadwal:
    jadwal = get_jadwal_or_404(db, id_jadwal)
    update_data = jadwal_update.dict(exclude_unset=True)

    for key, value in update_data.items():
        setattr(jadwal, key, value)

    db.commit()
    db.refresh(jadwal)
    return jadwal

def delete_jadwal(db: Session, id_jadwal: int) -> Jadwal:
    jadwal = get_jadwal_or_404(db, id_jadwal)
    db.delete(jadwal)
    db.commit()
    return jadwal
