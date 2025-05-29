# app/models/user.py
import enum
from app.models.kelas_mahasiswa import KelasMahasiswa
from app.database import Base
from sqlalchemy.orm import relationship
from sqlalchemy import Column, BigInteger, String, Enum, CheckConstraint, Integer

class RoleEnum(str, enum.Enum):
    mahasiswa = "mahasiswa"
    dosen = "dosen"

class User(Base):
    __tablename__ = "users"

    user_id = Column(Integer, primary_key=True, index=True, nullable=False)
    name = Column(String, nullable=False)
    email = Column(String, unique=True, index=True, nullable=False)
    password = Column(String, nullable=False)
    role = Column(Enum(RoleEnum), nullable=False)
    nrp = Column(BigInteger, unique=True, nullable=True)
    nip = Column(BigInteger, unique=True, nullable=True)

    __table_args__ = (
        CheckConstraint(
            "(role = 'mahasiswa' AND nrp IS NOT NULL AND nip IS NULL) OR (role = 'dosen' AND nip IS NOT NULL AND nrp IS NULL)",
            name="chk_role_nrp_nip"
        ),
    )

    kelas = relationship("KelasMahasiswa", back_populates="user")  # Many-to-many via KelasMahasiswa
