# Face Recognition for Presence Detection

## Overview

_Work in progress..._

## Installation

1. Clone the repository.

   ```bash
   git clone <repository-url>
   ```

2. Ensure Python 3.8 or higher is installed.

3. Create and activate a virtual environment.

   ```bash
   python -m venv venv
   source venv/bin/activate  # On Windows, use `venv\Scripts\activate`
   ```

4. Install the required packages.

   ```bash
   pip install -r requirements.txt
   ```

5. Set up PostgreSQL database.

   - Open PostgreSQL command line:

     ```bash
     psql -U postgres
     ```

   - Create the database:

     ```sql
     CREATE DATABASE presensi;
     ```

6. Configure environment variables. Copy the template file `.env.example` to `.env`.

   ```bash
   cp .env.example .env
   ```

   Edit the `.env` file to match your database configuration.

## Usage

1. Navigate to the project directory.

   ```bash
   cd /path/to/your/project
   ```

2. Update the `.env` file with your database credentials.

3. Activate the virtual environment.

   ```bash
   source venv/bin/activate  # On Windows, use `venv\Scripts\activate`
   ```

4. Start the FastAPI server.

   ```bash
   uvicorn app.main:app --reload
   ```

5. Run the PHP server for the frontend.

   ```bash
   php -S localhost:8000 -t web/public
   ```

   > Note: You can change the port number if needed.
