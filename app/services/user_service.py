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