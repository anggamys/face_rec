from datetime import datetime
from zoneinfo import ZoneInfo
from sqlalchemy.orm import Session
from fastapi import HTTPException, UploadFile
from app.models import AbsenSession, Jadwal, User
from scripts.face_recognition_integration import recognize_and_absen_from_bytes

def open_absen_session(db: Session, id_jadwal: int, current_user: User):
    if current_user.role != "dosen":
        raise HTTPException(status_code=403, detail="Only lecturers can open attendance sessions.")

    jadwal = db.query(Jadwal).filter_by(id_jadwal=id_jadwal).first()
    if not jadwal:
        raise HTTPException(status_code=404, detail="Jadwal not found.")

    existing = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal, is_active=True).first()
    if existing:
        raise HTTPException(status_code=400, detail="An active session already exists.")

    session = AbsenSession(
        id_jadwal=id_jadwal,
        opened_by=current_user.user_id,
        waktu_mulai=datetime.now(ZoneInfo("Asia/Jakarta")),
        is_active=True
    )
    db.add(session)
    db.commit()
    db.refresh(session)

    return session

def close_absen_session(db: Session, id_session: int, current_user: User):
    session = db.query(AbsenSession).filter_by(id_session=id_session).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    if session.opened_by != current_user.user_id and current_user.role != "admin":
        raise HTTPException(status_code=403, detail="You cannot close this session.")
    if not session.is_active:
        raise HTTPException(status_code=400, detail="Session is already closed.")

    session.is_active = False
    session.waktu_berakhir = datetime.now(ZoneInfo("Asia/Jakarta"))
    db.commit()

    return session

def get_all_sessions(db: Session):
    return db.query(AbsenSession).order_by(AbsenSession.waktu_mulai.desc()).all()

def get_session_by_id_jadwal(db: Session, id_jadwal: int):
    session = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    return session

async def process_face_recognition(db: Session, id_session: int, image: UploadFile):
    session = db.query(AbsenSession).filter_by(id_session=id_session).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    if not session.is_active:
        raise HTTPException(status_code=400, detail="Session is closed.")

    jadwal = db.query(Jadwal).filter_by(id_jadwal=session.id_jadwal).first()
    if not jadwal:
        raise HTTPException(status_code=404, detail="Jadwal not found.")

    try:
        image_bytes = await image.read()
        result = recognize_and_absen_from_bytes(
            image_bytes=image_bytes,
            id_jadwal=session.id_jadwal,
            id_matkul=jadwal.id_matkul,
            id_session=session.id_session
        )
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Face recognition processing failed: {str(e)}")
