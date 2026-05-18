from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/vision-statements", tags=["vision-statements"])


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM vision_statements ORDER BY created_at ASC")
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM vision_statements WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO vision_statements (title, title_en, points, points_en) VALUES (%s, %s, %s, %s)",
            (body.get("title"), body.get("title_en"), body.get("points"), body.get("points_en")),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM vision_statements WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM vision_statements WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE vision_statements SET title=%s, title_en=%s, points=%s, points_en=%s WHERE id=%s",
            (body.get("title"), body.get("title_en"), body.get("points"), body.get("points_en"), item_id),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM vision_statements WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM vision_statements WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("DELETE FROM vision_statements WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
