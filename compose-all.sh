#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$ROOT_DIR/backend"
FRONTEND_DIR="$ROOT_DIR/frontend"
CMS_DIR="$ROOT_DIR/cms"

if ! command -v docker >/dev/null 2>&1; then
  echo "[ERROR] docker command not found. Install Docker Desktop first."
  exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
  echo "[ERROR] docker compose is not available."
  exit 1
fi

echo "[1/3] Building and starting backend services (nginx, app, db)..."
(
  cd "$BACKEND_DIR"
  docker compose up --build -d
)

echo "[2/3] Building and starting frontend service..."
(
  cd "$FRONTEND_DIR"
  docker compose up --build -d
)

echo "[3/3] Building and starting cms service..."
(
  cd "$CMS_DIR"
  docker compose up --build -d
)

echo "[DONE] All services are up."
echo "- Frontend: http://localhost"
echo "- Backend (nginx): http://localhost:8080"
echo "- CMS (direct): http://localhost:8090"
echo "- CMS (via nginx): http://localhost:81"