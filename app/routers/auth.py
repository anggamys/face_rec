from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from app.config import SessionLocal
from app.schemas.user import UserCreate, UserLogin, UserOut
from app.services import auth_service
from pydantic import BaseModel
from app.models.user import User  # Ensure this import is correct

router = APIRouter(prefix="/auth", tags=["Auth"])

# Dependency to get the database session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Response model for login
class LoginResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    user: UserOut

# Register Route
@router.post("/register", response_model=UserOut)
def register(user: UserCreate, db: Session = Depends(get_db)):
    existing_user = db.query(User).filter(User.email == user.email).first()  # Corrected User model import
    
    if existing_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email already registered"
        )
    
    new_user = auth_service.register_user(db, user)
    return new_user

# Login Route with structured response
@router.post("/login", response_model=LoginResponse)
def login(user: UserLogin, db: Session = Depends(get_db)):
    find_user = auth_service.authenticate_user(db, user.email, user.password)
    if not find_user:
        raise HTTPException(status_code=401, detail="Invalid credentials")
    token = auth_service.create_access_token({"sub": find_user.email})
    return {"access_token": token, "user": find_user}

# Logout Route (front-end should handle token deletion)
@router.post("/logout")
def logout():
    # Handle token invalidation here if required
    return {"message": "Logged out"}
