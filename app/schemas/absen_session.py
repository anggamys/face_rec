from pydantic import BaseModel

class AbsenSessionBase(BaseModel):
    id_jadwal: int
    opened_by: int | None = None
    waktu_mulai: str | None = None
    waktu_berakhir: str | None = None
    is_active: bool = True

class AbsenSessionCreate(AbsenSessionBase):
    pass

class AbsenSessionResponse(AbsenSessionBase):
    id_session: int
    id_jadwal: int
    opened_by: int | None = None
    waktu_mulai: str | None = None
    waktu_berakhir: str | None = None
    is_active: bool = True

    class Config:
        orm_mode = True