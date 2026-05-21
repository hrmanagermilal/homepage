from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/together", tags=["together"])


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM together_items WHERE is_active = 1 ORDER BY `order`"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM together_items WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute(
            """INSERT INTO together_items (title, description, image, is_active, `order`)
               VALUES (%s, %s, %s, %s, %s)""",
            (
                body.get("title"),
                body.get("description"),
                body.get("image"),
                body.get("is_active", True),
                body.get("order", 0),
            ),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM together_items WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM together_items WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            """UPDATE together_items SET title=%s, description=%s, image=%s,
               is_active=%s, `order`=%s WHERE id=%s""",
            (
                body.get("title"),
                body.get("description"),
                body.get("image"),
                body.get("is_active"),
                body.get("order"),
                item_id,
            ),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM together_items WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM together_items WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("DELETE FROM together_items WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
