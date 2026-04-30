# Frontend (React + MUI)

This app is a React + Material UI frontend for the backend APIs.

## Setup

1. Install dependencies

```bash
cd frontend
npm install
```

2. Create env file

```bash
copy .env.example .env
```

3. Start dev server

```bash
npm run dev
```

The app runs at http://localhost:5173.

## Backend API

Set backend URL in .env:

```bash
VITE_API_BASE_URL=http://localhost:8000
```

Used endpoints:
- GET /api
- GET /api/sermons
- GET /api/announcements
- GET /api/departments
- POST /api/auth/login
