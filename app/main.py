import uvicorn
from fastapi import FastAPI
from app.routers import auth, matakuliah, kelas, jadwal, absen, mahasiswa, absen_session
from app.database import Base, engine

Base.metadata.create_all(bind=engine)

app = FastAPI()

app.include_router(absen.router)
app.include_router(auth.router)
app.include_router(jadwal.router)
app.include_router(kelas.router)
app.include_router(matakuliah.router)
app.include_router(mahasiswa.router)
app.include_router(absen_session.router)

if __name__ == "__main__":
    uvicorn.run("app.main:app", host="0.0.0.0", port=8000, reload=True)