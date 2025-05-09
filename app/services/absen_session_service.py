from datetime import datetime
from sqlalchemy.orm import Session
from fastapi import HTTPException, status
from app.models import AbsenSession, Jadwal, User

def open_absen_session(db: Session, id_jadwal: int, current_user: User):
    if current_user.role != "dosen":
        raise HTTPException(status_code=403, detail="Only lecturers can open attendance sessions.")

    jadwal = db.query(Jadwal).filter_by(id_jadwal=id_jadwal).first()
    if not jadwal:
        raise HTTPException(status_code=404, detail="Jadwal not found.")

    existing = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal, is_active=True).first()
    if existing:
        raise HTTPException(status_code=400, detail="An active attendance session already exists.")

    session = AbsenSession(
        id_jadwal=id_jadwal,
        id_kelas=jadwal.kode_kelas,  # Asumsikan ini id_kelas, sesuaikan jika nama field beda
        id_mata_kuliah=jadwal.kelas.matakuliah[0].id_matkul if jadwal.kelas.matakuliah else None,
        opened_by=current_user.user_id,
        waktu_mulai=datetime.utcnow(),
        is_active=True
    )
    db.add(session)
    db.commit()
    db.refresh(session)
    return session

def close_absen_session(db: Session, session_id: int, current_user: User):
    session = db.query(AbsenSession).filter_by(id_session=session_id).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    if session.opened_by != current_user.user_id and current_user.role != "admin":
        raise HTTPException(status_code=403, detail="You are not allowed to close this session.")
    if not session.is_active:
        raise HTTPException(status_code=400, detail="Session is already closed.")

    session.is_active = False
    session.waktu_berakhir = datetime.utcnow()
    db.commit()
    db.refresh(session)
    return session

def get_all_sessions(db: Session):
    return db.query(AbsenSession).order_by(AbsenSession.waktu_mulai.desc()).all()

def get_session_by_id(db: Session, session_id: int):
    session = db.query(AbsenSession).filter_by(id_session=session_id).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    return session
