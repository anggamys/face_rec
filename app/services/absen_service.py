from sqlalchemy.orm import Session
from app.models.absen import Absen
from app.models.user import User
from app.models.kelas import Kelas
from app.models.jadwal import Jadwal
from app.schemas.absen import AbsenCreate
from typing import List
from fastapi import HTTPException

def create_absen(db: Session, absen_data: AbsenCreate) -> Absen:
    # Ambil sesi absensi berdasarkan id_jadwal (bukan id_session)
    absensi_session = db.query(Jadwal).filter(Jadwal.id_jadwal == absen_data.id_jadwal).first()
    
    if not absensi_session:
        raise HTTPException(status_code=404, detail="Jadwal tidak ditemukan.")
    
    # Pastikan sesi absensi masih aktif
    if not absensi_session.is_active:
        raise HTTPException(status_code=400, detail="Session is closed.")
    
    # Ambil mahasiswa berdasarkan user_id
    mahasiswa = db.query(User).filter(User.user_id == absen_data.user_id, User.role == "mahasiswa").first()
    
    if not mahasiswa:
        raise HTTPException(status_code=404, detail="Mahasiswa not found.")
    
    # Pastikan mahasiswa terdaftar di kelas yang sesuai
    mahasiswa_in_kelas = any(kelas.id_kelas == absen_data.id_kelas for kelas in mahasiswa.kelas)
    if not mahasiswa_in_kelas:
        raise HTTPException(status_code=404, detail="Mahasiswa is not in the specified kelas.")
    
    # Pastikan mahasiswa hanya bisa absen satu kali per jadwal
    existing_absen = db.query(Absen).filter(Absen.id_mahasiswa == absen_data.user_id, Absen.id_jadwal == absen_data.id_jadwal).first()
    if existing_absen:
        raise HTTPException(status_code=400, detail="Mahasiswa has already submitted an attendance for this session.")

    # Buat data absensi baru
    new_absen = Absen(
        id_jadwal=absensi_session.id_jadwal,
        id_mahasiswa=mahasiswa.user_id,
        status=absen_data.status,
    )
    
    db.add(new_absen)
    db.commit()
    db.refresh(new_absen)
    return new_absen

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
