from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/sermons", tags=["sermons"])

_SELECT = """
    SELECT s.*, sc.title AS category_title, sc.image AS category_image
    FROM sermons s
    LEFT JOIN sermon_categories sc ON s.category_id = sc.id
"""


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM sermons")
        total = cur.fetchone()["total"]
        cur.execute(
            _SELECT + " ORDER BY s.sermon_date DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    return paginated(serialize_all(rows), total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(_SELECT + " WHERE s.id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))
