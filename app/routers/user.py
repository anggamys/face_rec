from fastapi import APIRouter, Depends
from app.schemas.user import UsersMahasiswa
from typing import List
from app.services import user_service
from sqlalchemy.orm import Session
from app.models.user import User
from app.config import get_db, get_current_user
from app.schemas.user import UserResponse

router = APIRouter(prefix="/user", tags=["User"])

@router.get("/mahasiswa", response_model=List[UsersMahasiswa])
def get_all_mahasiswa(
    db: Session = Depends(get_db),
):    
    return user_service.get_all_mahasiswa(db)

@router.get("/{user_id}", response_model=UserResponse)
def get_user_by_id(
    user_id: int,
    db: Session = Depends(get_db),
):
    return user_service.get_user_by_id(db, user_id)

@router.get("/nrp/{nrp}", response_model=UserResponse)
def get_user_by_nrp(
    nrp: int,
    db: Session = Depends(get_db),
):
    return user_service.get_user_by_nrp(db, nrp)  
