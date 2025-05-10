from datetime import datetime
from pydantic import BaseModel

class AbsenSessionBase(BaseModel):
    id_jadwal: int
    opened_by: int | None = None
    waktu_mulai: datetime | None = None  # ✅ ubah ke datetime
    waktu_berakhir: datetime | None = None
    is_active: bool = True

class AbsenSessionCreate(AbsenSessionBase):
    pass

class AbsenSessionResponse(AbsenSessionBase):
    id_session: int

    class Config:
        orm_mode = True
