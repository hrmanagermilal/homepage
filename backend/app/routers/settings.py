from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db
from ..response import success, error

router = APIRouter(prefix="/settings", tags=["settings"])

_ALLOWED_THEMES = {"dark-green", "dark-blue", "dark-brown"}


@router.get("/theme")
def get_theme(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT value FROM site_settings WHERE `key` = 'theme'")
        row = cur.fetchone()
    return success({"theme": row["value"] if row else "dark-green"})


@router.put("/theme")
def set_theme(body: dict, db: Connection = Depends(get_db)):
    theme = body.get("theme", "")
    if theme not in _ALLOWED_THEMES:
        return error("Invalid theme", "INVALID_THEME", 400)
    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO site_settings (`key`, value) VALUES ('theme', %s)"
            " ON DUPLICATE KEY UPDATE value = %s",
            (theme, theme),
        )
    return success({"theme": theme})
