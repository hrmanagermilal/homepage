from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/sections", tags=["sections"])


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM section_titles ORDER BY created_at ASC")
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM section_titles WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO section_titles (section_key, title, subtitle) VALUES (%s, %s, %s)",
            (body.get("section_key"), body.get("title"), body.get("subtitle")),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM section_titles WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM section_titles WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE section_titles SET section_key=%s, title=%s, subtitle=%s WHERE id=%s",
            (body.get("section_key"), body.get("title"), body.get("subtitle"), item_id),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM section_titles WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM section_titles WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("DELETE FROM section_titles WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
