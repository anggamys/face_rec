from sqlalchemy import Column, String, ForeignKey, BigInteger
from sqlalchemy.orm import relationship
from app.database import Base

class KelasMahasiswa(Base):
    __tablename__ = 'kelas_mahasiswa'
    
    kode_kelas = Column(String, ForeignKey('kelas.kode_kelas'), primary_key=True)
    nrp_mahasiswa = Column(BigInteger, ForeignKey('users.nrp'), primary_key=True)
    
    kelas = relationship("Kelas", back_populates="mahasiswa")
    user = relationship("User", back_populates="kelas")
