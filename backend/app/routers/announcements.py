from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/announcements", tags=["announcements"])

VALID_CATEGORIES = {"general", "event", "urgent"}


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM announcements")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT * FROM announcements ORDER BY is_pinned DESC, created_at DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    return paginated(serialize_all(rows), total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM announcements WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    with db.cursor() as cur:
        cur.execute(
            "UPDATE announcements SET views = views + 1 WHERE id = %s", (item_id,)
        )
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    title = (body.get("title") or "").strip()
    content = (body.get("content") or "").strip()
    category = body.get("category", "general")
    if not title or not content:
        return error("title and content are required", "VALIDATION_ERROR", 400)
    if category not in VALID_CATEGORIES:
        return error(f"category must be one of {VALID_CATEGORIES}", "VALIDATION_ERROR", 400)
    with db.cursor() as cur:
        cur.execute(
            """INSERT INTO announcements (title, content, category, is_pinned, author_id)
               VALUES (%s, %s, %s, %s, %s)""",
            (title, content, category, body.get("is_pinned", False), body.get("author_id")),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM announcements WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM announcements WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE announcements SET title=%s, content=%s, category=%s, is_pinned=%s WHERE id=%s",
            (body.get("title"), body.get("content"), body.get("category"), body.get("is_pinned"), item_id),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM announcements WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM announcements WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("DELETE FROM announcements WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
