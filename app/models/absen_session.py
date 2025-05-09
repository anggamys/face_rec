from sqlalchemy import Column, Integer, DateTime, Boolean, ForeignKey
from sqlalchemy.orm import relationship
from app.database import Base
from datetime import datetime

class AbsenSession(Base):
    __tablename__ = "absen_session"

    id_session = Column(Integer, primary_key=True, index=True)
    id_jadwal = Column(Integer, ForeignKey("jadwal.id_jadwal", onupdate="CASCADE", ondelete="CASCADE"), nullable=False)
    opened_by = Column(Integer, ForeignKey("users.user_id", onupdate="CASCADE", ondelete="SET NULL"), nullable=True)
    waktu_mulai = Column(DateTime, default=datetime.utcnow)
    waktu_berakhir = Column(DateTime, nullable=True)
    is_active = Column(Boolean, default=True)

    jadwal = relationship("Jadwal")
    dosen = relationship("User")
