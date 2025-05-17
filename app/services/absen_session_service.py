from datetime import datetime
from zoneinfo import ZoneInfo
from sqlalchemy.orm import Session
from fastapi import HTTPException, BackgroundTasks
from app.models import AbsenSession, Jadwal, User
import threading
from scripts import face_recognition_integration

face_recognition_stop_events = {}

def open_absen_session(db: Session, id_jadwal: int, current_user: User, background_tasks: BackgroundTasks):
    if current_user.role != "dosen":
        raise HTTPException(status_code=403, detail="Only lecturers can open attendance sessions.")

    jadwal = db.query(Jadwal).filter_by(id_jadwal=id_jadwal).first()
    if not jadwal:
        raise HTTPException(status_code=404, detail="Jadwal not found.")

    existing = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal).first()
    if existing:
        raise HTTPException(status_code=400, detail="An active attendance session already exists.")

    session = AbsenSession(
        id_jadwal=id_jadwal,
        opened_by=current_user.user_id,
        waktu_mulai=datetime.now(ZoneInfo("Asia/Jakarta")),
        is_active=True
    )

    db.add(session)
    db.commit()
    db.refresh(session)

    # Start script detection 
    if session.is_active:
        stop_detection = threading.Event()
        face_recognition_stop_events[id_jadwal] = stop_detection

        background_tasks.add_task(
            face_recognition_integration.run_face_recognition,
            id_jadwal, jadwal.id_matkul, current_user, stop_detection
        )
    return session

def close_absen_session(db: Session, id_jadwal: int, current_user: User):
    session = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    if session.opened_by != current_user.user_id and current_user.role != "admin":
        raise HTTPException(status_code=403, detail="You are not allowed to close this session.")
    if not session.is_active:
        raise HTTPException(status_code=400, detail="Session is already closed.")

    session.is_active = False
    session.waktu_berakhir = datetime.now(ZoneInfo("Asia/Jakarta"))

    db.commit()
    db.refresh(session)

    # Stop the face recognition script
    stop_event = face_recognition_stop_events.get(id_jadwal)
    if stop_event:
        stop_event.set()
        del face_recognition_stop_events[id_jadwal]
    else:
        raise HTTPException(status_code=404, detail="No active face recognition session found.")
    
    return session

def get_all_sessions(db: Session):
    return db.query(AbsenSession).order_by(AbsenSession.waktu_mulai.desc()).all()

def get_session_by_id_jadwal(db: Session, id_jadwal: int):
    session = db.query(AbsenSession).filter_by(id_jadwal=id_jadwal).first()
    if not session:
        raise HTTPException(status_code=404, detail="Session not found.")
    return session
