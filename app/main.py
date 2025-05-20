import uvicorn
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routers import auth, matakuliah, kelas, jadwal, absen, mahasiswa, absen_session
from app.database import Base, engine

Base.metadata.create_all(bind=engine)

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Ubah sesuai asal domain frontend kamu, misalnya ["http://localhost:3000"]
    allow_credentials=True,
    allow_methods=["*"],  # Mengizinkan semua method: POST, GET, OPTIONS, dll.
    allow_headers=["*"],  # Mengizinkan semua header
)

app.include_router(absen.router)
app.include_router(auth.router)
app.include_router(jadwal.router)
app.include_router(kelas.router)
app.include_router(matakuliah.router)
app.include_router(mahasiswa.router)
app.include_router(absen_session.router)

if __name__ == "__main__":
    uvicorn.run("app.main:app", host="0.0.0.0", port=8000, reload=True)