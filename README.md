# Face Recognition for Presence Detection

A face recognition-based attendance system using FastAPI for the backend and PHP for the frontend.

## Installation

### Clone the repository

```bash
git clone <repository-url>
cd face-recognition-presence
```

### Set up a Python virtual environment

```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

### Install dependencies

```bash
pip install -r requirements.txt
```

### Set up PostgreSQL

```sql
CREATE DATABASE presensi;
```

### Configure environment variables

```bash
cp .env.example .env
```

> Edit `.env` with your database credentials.

---

## Usage

### Activate the virtual environment

```bash
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

### Start the FastAPI server

```bash
uvicorn app.main:app --reload
```

> The server will run on `http://localhost:8000`. You can access the API documentation at `http://localhost:8000/docs`.

### Run the PHP frontend

```bash
php -S localhost:8081 -t web/public
```

> The frontend will run on `http://localhost:8081`.

**Warning:** To update your existing database to match the current database model, run the following command:

```bash
python3 -m app.reset_db
```
