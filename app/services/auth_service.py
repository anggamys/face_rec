from sqlalchemy.orm import Session
from app.models.user import User
from app.schemas.user import UserCreate, UserOut
from passlib.context import CryptContext
from jose import jwt
from datetime import datetime, timedelta
import os

# Load secret key from environment variable
SECRET_KEY = os.getenv("SECRET_KEY", "")  # It's better to provide a default for local dev
ALGORITHM = "HS256"

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def hash_password(password: str):
    return pwd_context.hash(password)

def verify_password(plain_password: str, hashed_password: str):
    return pwd_context.verify(plain_password, hashed_password)

def create_access_token(data: dict, expires_delta: timedelta = timedelta(hours=1)):
    to_encode = data.copy()
    to_encode.update({"exp": datetime.utcnow() + expires_delta})
    return jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)

def register_user(db: Session, user: UserCreate):
    # Check if email already exists
    existing_user = db.query(User).filter(User.email == user.email).first()
    if existing_user:
        raise ValueError("Email already registered")  # Could raise a custom exception or HTTPException
    
    hashed_password = hash_password(user.password)
    db_user = User(
        name=user.name, 
        email=user.email, 
        password=hashed_password, 
        role=user.role, 
        nrp=user.nrp, 
        nip=user.nip
    )
    
    try:
        db.add(db_user)
        db.commit()
        db.refresh(db_user)
        return UserOut.from_orm(db_user)  # Return serialized response with Pydantic model
    except Exception as e:
        db.rollback()  # Rollback the transaction in case of an error
        raise ValueError(f"Error creating user: {e}")

def authenticate_user(db: Session, email: str, password: str):
    user = db.query(User).filter(User.email == email).first()
    if not user or not verify_password(password, user.password):
        return None
    return user  # It's better to return the full user object, but consider using a response model to filter sensitive info
