from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/notice", tags=["notice"])


def _format_row(row):
    if row is None:
        return None
    d = serialize(row)
    # Expose writer_name as "author" for frontend compatibility
    d["author"] = d.pop("writer_name", None)
    # Format created_date as "YYYY. MM. DD"
    created_date = d.pop("created_date", None)
    if created_date:
        d["date"] = created_date.strftime("%Y. %m. %d") if hasattr(created_date, "strftime") else str(created_date)
    else:
        d["date"] = None
    return d


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    q: str = Query(""),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        if q:
            pattern = f"%{q}%"
            cur.execute(
                "SELECT COUNT(*) as total FROM notice WHERE title LIKE %s OR writer_name LIKE %s",
                (pattern, pattern),
            )
            total = cur.fetchone()["total"]
            cur.execute(
                "SELECT * FROM notice WHERE title LIKE %s OR writer_name LIKE %s"
                " ORDER BY created_date DESC, id DESC LIMIT %s OFFSET %s",
                (pattern, pattern, limit, offset),
            )
        else:
            cur.execute("SELECT COUNT(*) as total FROM notice")
            total = cur.fetchone()["total"]
            cur.execute(
                "SELECT * FROM notice ORDER BY created_date DESC, id DESC LIMIT %s OFFSET %s",
                (limit, offset),
            )
        rows = cur.fetchall()
    return paginated([_format_row(r) for r in rows], total, page, limit)


@router.get("/{notice_id}")
def get_one(notice_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM notice WHERE id = %s", (notice_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    with db.cursor() as cur:
        cur.execute("UPDATE notice SET views = views + 1 WHERE id = %s", (notice_id,))
    db.commit()
    return success(_format_row(row))
