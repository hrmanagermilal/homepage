from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/bulletins", tags=["bulletins"])


def _attach_images(bulletin: dict, db: Connection) -> dict:
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM bulletin_images WHERE bulletin_id = %s ORDER BY `order`",
            (bulletin["id"],),
        )
        bulletin["images"] = serialize_all(cur.fetchall())
    return bulletin


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM bulletins")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT * FROM bulletins ORDER BY year DESC, week_number DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    data = [_attach_images(serialize(r), db) for r in rows]
    return paginated(data, total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM bulletins WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(_attach_images(serialize(row), db))
