from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/news", tags=["news"])


def _attach_comments(news_item: dict, db: Connection) -> dict:
    news_item["comments"] = []
    return news_item


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM news")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT * FROM news ORDER BY created_at DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    return paginated(serialize_all(rows), total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM news WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    with db.cursor() as cur:
        cur.execute("UPDATE news SET views = views + 1 WHERE id = %s", (item_id,))
    return success(_attach_comments(serialize(row), db))
