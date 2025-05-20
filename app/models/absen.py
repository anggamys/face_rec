from sqlalchemy import Column, Integer, BigInteger, ForeignKey, Enum
from sqlalchemy.orm import relationship
from app.database import Base
import enum

class StatusEnum(str, enum.Enum):
    hadir = "hadir",
    alpha = "alpha"

class Absen(Base):
    __tablename__ = "absen"

    id_absen = Column(Integer, primary_key=True, index=True)
    id_mahasiswa = Column(BigInteger, ForeignKey("users.nrp", onupdate="CASCADE", ondelete="CASCADE"), nullable=False)
    id_jadwal = Column(Integer, ForeignKey("jadwal.id_jadwal", onupdate="CASCADE", ondelete="CASCADE"), nullable=False)
    status = Column(Enum(StatusEnum), nullable=False)

    mahasiswa = relationship("User")
    jadwal = relationship("Jadwal")
