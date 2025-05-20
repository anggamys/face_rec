from sqlalchemy.orm import Session
from typing import List
from app.models.user import User
from app.schemas.user import UsersMahasiswa

def get_all_mahasiswa(db: Session) -> List[UsersMahasiswa]:
    mahasiswa_list = db.query(User).filter(User.role == "mahasiswa").all()
    results = []
    for m in mahasiswa_list:
        results.append(UsersMahasiswa(
            user_id=m.user_id,
            name=m.name,
            email=m.email,
            nrp=m.nrp,
            role=m.role
        ))

    return results

def get_user_by_id(db: Session, user_id: int) -> User:
    return db.query(User).filter(User.user_id == user_id).first()

def get_user_by_nrp(db: Session, nrp: int) -> User:
    return db.query(User).filter(User.nrp == nrp).first()