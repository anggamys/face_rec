from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.models.user import User
from app.config import get_db, get_current_user
from app.services import kelas_service
from app.schemas.kelas import KelasCreate, KelasUpdate, KelasResponse

router = APIRouter(prefix='/kelas', tags=["Kelas"])

@router.post("/", response_model=KelasResponse)
def create_kelas(
    kelas: KelasCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Unauthorized")

    return kelas_service.create_kelas(db, kelas)

@router.get("/", response_model=List[KelasResponse])
def get_all_kelas(db: Session = Depends(get_db)):
    return kelas_service.get_all_kelas(db)

@router.get("/{kode_kelas}", response_model=KelasResponse)
def get_kelas_by_kode(kode_kelas: str, db: Session = Depends(get_db)):
    kelas = kelas_service.get_kelas_by_kode(db, kode_kelas)
    if not kelas:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Kelas tidak ditemukan")
    return kelas

@router.get("/matkul/{id_matkul}", response_model=List[KelasResponse])
def get_kelas_by_matkul(id_matkul: int, db: Session = Depends(get_db)):
    return kelas_service.get_kelas_by_matkul(db, id_matkul)

@router.put("/{kode_kelas}", response_model=KelasResponse)
def update_kelas(
    kode_kelas: str,
    kelas: KelasUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Unauthorized")

    updated_kelas = kelas_service.update_kelas(db, kode_kelas, kelas)
    if not updated_kelas:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Kelas tidak ditemukan")
    return updated_kelas

@router.delete("/{kode_kelas}")
def delete_kelas(
    kode_kelas: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Unauthorized")

    success = kelas_service.delete_kelas(db, kode_kelas)
    if not success:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Kelas tidak ditemukan")

    return {"message": "Kelas berhasil dihapus"}

# Jika masih pakai fungsi tambah mahasiswa/matkul terpisah
@router.post("/{kode_kelas}/mahasiswa")
def tambah_mahasiswa_ke_kelas(
    kode_kelas: str,
    mahasiswa_ids: List[str],
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Unauthorized")

    success = kelas_service.create_kelas_mahasiswa(db, kode_kelas, mahasiswa_ids)
    if not success:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Kelas tidak ditemukan")
    return {"message": "Mahasiswa berhasil ditambahkan ke kelas"}

@router.post("/{kode_kelas}/matakuliah")
def tambah_matakuliah_ke_kelas(
    kode_kelas: str,
    matkul_ids: List[int],
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    if current_user.role != "dosen":
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Unauthorized")

    success = kelas_service.create_kelas_matakuliah(db, kode_kelas, matkul_ids)
    if not success:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Kelas tidak ditemukan")
    return {"message": "Matakuliah berhasil ditambahkan ke kelas"}

# Opsional include sub-router jadwal jika sudah jadi
# router.include_router(jadwal.router, prefix="/{kode_kelas}/jadwal")
