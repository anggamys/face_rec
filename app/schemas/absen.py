from pydantic import BaseModel

class AbsenBase(BaseModel):
    users_nrp: int
    id_jadwal: int
    detection_time: str | None = None  # Optional field for detection time

class AbsenCreate(AbsenBase):
    status: str    
    
class AbsenResponse(BaseModel):
    id_absen: int
    id_jadwal: int
    users_nrp: int
    status: str

    class Config:
        orm_mode = True
