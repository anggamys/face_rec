from pydantic import BaseModel

class AbsenBase(BaseModel):
    id_session: int  # Ini diasumsikan = id_jadwal
    user_id: int

class AbsenCreate(AbsenBase):
    status: str      # status seperti 'hadir', 'izin', 'sakit'

class AbsenResponse(BaseModel):
    id_absen: int
    id_jadwal: int
    id_mahasiswa: int
    status: str

    class Config:
        orm_mode = True
