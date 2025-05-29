from sqlalchemy import Column, String, ForeignKey
from sqlalchemy.orm import relationship
from app.database import Base

class KelasMahasiswa(Base):
    __tablename__ = 'kelas_mahasiswa'
    
    kode_kelas = Column(String, ForeignKey('kelas.kode_kelas'), primary_key=True)
    nrp_mahasiswa = Column(String, ForeignKey('users.nrp'), primary_key=True)
    
    # Relationship to Kelas
    kelas = relationship("Kelas", back_populates="mahasiswa")
    # Relationship to User (untuk mahasiswa)
    user = relationship("User", back_populates="kelas")
