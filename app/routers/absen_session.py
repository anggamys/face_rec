from fastapi import APIRouter, Depends, Path
from sqlalchemy.orm import Session
from typing import List

from app.config import get_db, get_current_user
from app.models import User
from app.schemas.absen_session import AbsenSessionResponse
from app.services import absen_session_service

router = APIRouter(
    prefix="/absen-session",
    tags=["Absen Session"]
)

@router.post("/open/{id_jadwal}", response_model=AbsenSessionResponse)
def open_session(
    id_jadwal: int = Path(..., description="ID jadwal yang ingin dibuka sesi absensinya"),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    return absen_session_service.open_absen_session(db, id_jadwal, current_user)


@router.post("/close/{id_jadwal}", response_model=AbsenSessionResponse)
def close_session(
    id_jadwal: int = Path(..., description="ID jadwal untuk sesi absensi yang ingin ditutup"),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    return absen_session_service.close_absen_session(db, id_jadwal, current_user)


@router.get("/", response_model=List[AbsenSessionResponse])
def get_all_sessions(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    return absen_session_service.get_all_sessions(db)


@router.get("/{id_jadwal}", response_model=AbsenSessionResponse)
def get_session_by_id_jadwal(
    id_jadwal: int = Path(..., description="ID jadwal untuk sesi absensi"),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    return absen_session_service.get_session_by_id_jadwal(db, id_jadwal)
