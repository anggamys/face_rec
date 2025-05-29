from sqlalchemy import insert
from sqlalchemy.orm import Session
from sqlalchemy.exc import IntegrityError
from app.models.kelas import Kelas
from app.models.user import User
from app.models.matakuliah import Matakuliah
from app.models.kelas_mahasiswa import KelasMahasiswa
from app.models.kelas_matkul import kelas_matkul
from app.schemas.kelas import KelasCreate, KelasUpdate
from typing import List, Optional

# Validate mahasiswa based on NRP
def validate_mahasiswa(db: Session, mahasiswa_ids: List[str]) -> List[User]:
    valid_mahasiswa = db.query(User).filter(User.nrp.in_(mahasiswa_ids)).all()
    if len(valid_mahasiswa) != len(mahasiswa_ids):
        raise ValueError("Some mahasiswa (students) do not exist.")
    return valid_mahasiswa

# Validate matakuliah based on id_matkul
def validate_matakuliah(db: Session, matkul_ids: List[int]) -> List[Matakuliah]:
    valid_matakuliah = db.query(Matakuliah).filter(Matakuliah.id_matkul.in_(matkul_ids)).all()
    if len(valid_matakuliah) != len(matkul_ids):
        raise ValueError("Some matakuliah (subjects) do not exist.")
    return valid_matakuliah

# Create a new kelas
def create_kelas(db: Session, kelas: KelasCreate) -> dict:
    valid_mahasiswa = validate_mahasiswa(db, kelas.mahasiswa)
    valid_matakuliah = validate_matakuliah(db, kelas.matakuliah)

    new_kelas = Kelas(kode_kelas=kelas.kode_kelas, nama_kelas=kelas.nama_kelas)
    db.add(new_kelas)
    db.commit()
    db.refresh(new_kelas)

    db.bulk_save_objects([
        KelasMahasiswa(kode_kelas=new_kelas.kode_kelas, nrp_mahasiswa=m.nrp)
        for m in valid_mahasiswa
    ])

    db.execute(
        insert(kelas_matkul),
        [{"kode_kelas": new_kelas.kode_kelas, "id_matkul": mk.id_matkul} for mk in valid_matakuliah]
    )

    db.commit()

    return {
        "id_kelas": new_kelas.id_kelas,
        "kode_kelas": new_kelas.kode_kelas,
        "nama_kelas": new_kelas.nama_kelas,
        "mahasiswa": [m.nrp for m in valid_mahasiswa],
        "matakuliah": [mk.id_matkul for mk in valid_matakuliah],
    }

# Tambah mahasiswa ke kelas
def create_kelas_mahasiswa(db: Session, kode_kelas: str, mahasiswa_ids: List[str]) -> bool:
    kelas = db.query(Kelas).filter_by(kode_kelas=kode_kelas).first()
    if not kelas:
        return False

    valid_mahasiswa = validate_mahasiswa(db, mahasiswa_ids)

    for m in valid_mahasiswa:
        exists = db.query(KelasMahasiswa).filter_by(kode_kelas=kode_kelas, nrp_mahasiswa=m.nrp).first()
        if not exists:
            db.add(KelasMahasiswa(kode_kelas=kode_kelas, nrp_mahasiswa=m.nrp))
    db.commit()
    return True

# Tambah matakuliah ke kelas
def create_kelas_matakuliah(db: Session, kode_kelas: str, matkul_ids: List[int]) -> bool:
    kelas = db.query(Kelas).filter_by(kode_kelas=kode_kelas).first()
    if not kelas:
        return False

    valid_matakuliah = validate_matakuliah(db, matkul_ids)

    for mk in valid_matakuliah:
        exists = db.query(kelas_matkul).filter_by(kode_kelas=kode_kelas, id_matkul=mk.id_matkul).first()
        if not exists:
            db.add(kelas_matkul(kode_kelas=kode_kelas, id_matkul=mk.id_matkul))
    db.commit()
    return True

# Ambil semua kelas
def get_all_kelas(db: Session) -> List[dict]:
    kelas_list = db.query(Kelas).all()
    results = []
    for k in kelas_list:
        mahasiswa = db.query(KelasMahasiswa).filter_by(kode_kelas=k.kode_kelas).all()
        matakuliah = db.query(kelas_matkul).filter_by(kode_kelas=k.kode_kelas).all()
        results.append({
            "id_kelas": k.id_kelas,
            "kode_kelas": k.kode_kelas,
            "nama_kelas": k.nama_kelas,
            "mahasiswa": [m.nrp_mahasiswa for m in mahasiswa],
            "matakuliah": [mk.id_matkul for mk in matakuliah],
        })
    return results

# Ambil kelas berdasarkan kode
def get_kelas_by_kode(db: Session, kode_kelas: str) -> Optional[dict]:
    kelas = db.query(Kelas).filter_by(kode_kelas=kode_kelas).first()
    if not kelas:
        return None

    mahasiswa = db.query(KelasMahasiswa).filter_by(kode_kelas=kode_kelas).all()
    matakuliah = db.query(kelas_matkul).filter_by(kode_kelas=kode_kelas).all()

    return {
        "id_kelas": kelas.id_kelas,
        "kode_kelas": kelas.kode_kelas,
        "nama_kelas": kelas.nama_kelas,
        "mahasiswa": [m.nrp_mahasiswa for m in mahasiswa],
        "matakuliah": [mk.id_matkul for mk in matakuliah],
    }

# Ambil kelas berdasarkan ID matakuliah
def get_kelas_by_matkul(db: Session, id_matkul: int) -> List[dict]:
    kelas_matkul_records = db.query(kelas_matkul).filter_by(id_matkul=id_matkul).all()
    results = []
    for km in kelas_matkul_records:
        kelas = db.query(Kelas).filter_by(kode_kelas=km.kode_kelas).first()
        mahasiswa = db.query(KelasMahasiswa).filter_by(kode_kelas=kelas.kode_kelas).all()
        results.append({
            "id_kelas": kelas.id_kelas,
            "kode_kelas": kelas.kode_kelas,
            "nama_kelas": kelas.nama_kelas,
            "mahasiswa": [m.nrp_mahasiswa for m in mahasiswa],
            "matakuliah": [id_matkul],
        })
    return results

# Update kelas
def update_kelas(db: Session, kode_kelas: str, kelas_update: KelasUpdate) -> Optional[dict]:
    kelas = db.query(Kelas).filter_by(kode_kelas=kode_kelas).first()
    if not kelas:
        return None

    kelas.nama_kelas = kelas_update.nama_kelas

    if kelas_update.mahasiswa:
        valid_mahasiswa = validate_mahasiswa(db, kelas_update.mahasiswa)
        db.query(KelasMahasiswa).filter_by(kode_kelas=kode_kelas).delete()
        db.bulk_save_objects([
            KelasMahasiswa(kode_kelas=kode_kelas, nrp_mahasiswa=m.nrp) for m in valid_mahasiswa
        ])

    if kelas_update.matakuliah:
        valid_matakuliah = validate_matakuliah(db, kelas_update.matakuliah)
        db.query(kelas_matkul).filter_by(kode_kelas=kode_kelas).delete()
        db.execute(
            insert(kelas_matkul),
            [{"kode_kelas": kode_kelas, "id_matkul": mk.id_matkul} for mk in valid_matakuliah]
        )

    db.commit()
    db.refresh(kelas)

    return {
        "id_kelas": kelas.id_kelas,
        "kode_kelas": kelas.kode_kelas,
        "nama_kelas": kelas.nama_kelas,
        "mahasiswa": kelas_update.mahasiswa,
        "matakuliah": kelas_update.matakuliah,
    }

# Hapus kelas berdasarkan kode_kelas
def delete_kelas(db: Session, kode_kelas: str) -> bool:
    kelas = db.query(Kelas).filter_by(kode_kelas=kode_kelas).first()
    if not kelas:
        return False

    db.query(KelasMahasiswa).filter_by(kode_kelas=kode_kelas).delete()
    db.query(kelas_matkul).filter_by(kode_kelas=kode_kelas).delete()
    db.delete(kelas)
    db.commit()
    return True
