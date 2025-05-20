from pydantic import BaseModel, EmailStr, Field
from enum import Enum
from typing import Optional

class RoleEnum(str, Enum):
    mahasiswa = "mahasiswa"
    dosen = "dosen"

class UserBase(BaseModel):
    name: str
    email: EmailStr

class UserCreate(UserBase):
    password: str
    role: str = Field(..., pattern="^(mahasiswa|dosen)$")
    nrp: Optional[int] = None
    nip: Optional[int] = None

class UserOut(UserBase):
    user_id: int
    name: str
    role: str
    nrp: Optional[int] = None
    nip: Optional[int] = None

    class Config:
        from_attributes = True  # <- Ini penting!

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class UsersMahasiswa(BaseModel):
    user_id: int
    name: str
    email: EmailStr
    nrp: Optional[int] = None
    role: RoleEnum

class UserResponse(BaseModel):
    user_id: int
    name: str
    email: EmailStr
    nrp: Optional[int] = None
    nip: Optional[int] = None
    role: RoleEnum

    class Config:
        orm_mode = True