from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/quick-links", tags=["quick-links"])


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM quick_links ORDER BY created_at ASC")
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM quick_links WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    title = (body.get("title") or "").strip()
    url = (body.get("link") or "").strip()
    if not title or not url:
        return error("title and link are required", "VALIDATION_ERROR", 400)
    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO quick_links (title, link, image, `desc`) VALUES (%s, %s, %s, %s)",
            (title, url, body.get("image"), body.get("desc")),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM quick_links WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM quick_links WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE quick_links SET title=%s, link=%s, image=%s, `desc`=%s WHERE id=%s",
            (body.get("title"), body.get("link"), body.get("image"), body.get("desc"), item_id),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM quick_links WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM quick_links WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("DELETE FROM quick_links WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
