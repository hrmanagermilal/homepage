import os

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles

from .database import init_db
from .routers import (
    analytics,
    auth_routes,
    banner_image,
    settings,
    bulletins,
    departments,
    hero,
    landing_titles,
    members,
    ministry,
    notice,
    obituary,
    nextgen,
    album,
    parking_lot,
    parking_map,
    pastor_introduction,
    sections,
    sermons,
    service_times,
    shuttle_bus,
    together,
    tracking,
    users,
    vision_statements,
    quick_links,
    youtube,
)

app = FastAPI(title="밀알교회 API", version="2.0.0")

# ── CORS ─────────────────────────────────────────────────────────────────────
cors_origin = os.getenv("CORS_ORIGIN", "*")
app.add_middleware(
    CORSMiddleware,
    allow_origins=[cors_origin] if cors_origin != "*" else ["*"],
    allow_credentials=cors_origin != "*",
    allow_methods=["*"],
    allow_headers=["*"],
)

# ── Database startup ──────────────────────────────────────────────────────────
@app.on_event("startup")
def startup():
    init_db()


# ── Health check ──────────────────────────────────────────────────────────────
@app.get("/api/health")
def health():
    return {"status": "ok"}


# ── Routers ───────────────────────────────────────────────────────────────────
PREFIX = "/api"

app.include_router(auth_routes.router, prefix=PREFIX)
app.include_router(hero.router, prefix=PREFIX)
app.include_router(quick_links.router, prefix=PREFIX)
app.include_router(landing_titles.router, prefix=PREFIX)
app.include_router(sections.router, prefix=PREFIX)
app.include_router(vision_statements.router, prefix=PREFIX)
app.include_router(members.router, prefix=PREFIX)
app.include_router(bulletins.router, prefix=PREFIX)
app.include_router(sermons.router, prefix=PREFIX)
app.include_router(departments.router, prefix=PREFIX)
app.include_router(ministry.router, prefix=PREFIX)
app.include_router(notice.router, prefix=PREFIX)
app.include_router(obituary.router, prefix=PREFIX)
app.include_router(nextgen.router, prefix=PREFIX)
app.include_router(album.router, prefix=PREFIX)
app.include_router(service_times.router, prefix=PREFIX)
app.include_router(shuttle_bus.router, prefix=PREFIX)
app.include_router(parking_lot.router, prefix=PREFIX)
app.include_router(parking_map.router, prefix=PREFIX)
app.include_router(banner_image.router, prefix=PREFIX)
app.include_router(pastor_introduction.router, prefix=PREFIX)
app.include_router(together.router, prefix=PREFIX)
app.include_router(users.router, prefix=PREFIX)
app.include_router(tracking.router, prefix=PREFIX)
app.include_router(analytics.router, prefix=PREFIX)
app.include_router(settings.router, prefix=PREFIX)
app.include_router(youtube.router, prefix=PREFIX)


# ── Serve uploads as static files ─────────────────────────────────────────────
uploads_path = os.getenv("UPLOADS_PATH", "./uploads")
os.makedirs(uploads_path, exist_ok=True)
app.mount("/uploads", StaticFiles(directory=uploads_path), name="uploads")
