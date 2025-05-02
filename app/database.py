from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from dotenv import load_dotenv
import os

load_dotenv(".env")

# DATABASE_URL = "postgresql://postgres:root@localhost:5432/presensi"
DATABASE_URL = os.getenv("DATABASE_URL_ENV")

engine = create_engine(DATABASE_URL or "")
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()
