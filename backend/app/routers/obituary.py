from fastapi import APIRouter, Depends, HTTPException
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all

router = APIRouter()


def _row_to_dict(row):
    if row is None:
        return None
    d = serialize(row)
    d["is_active"] = bool(d.get("is_active"))
    if d.get("date"):
        d["date"] = d["date"].strftime("%Y. %m. %d") if hasattr(d["date"], "strftime") else str(d["date"])
    return d


@router.get("/obituary")
def get_obituary(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM obituary WHERE is_active = 1 ORDER BY date DESC, id DESC"
        )
        rows = cur.fetchall()
    return {"data": [_row_to_dict(r) for r in rows]}


@router.get("/obituary/{obituary_id}")
def get_obituary_by_id(obituary_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM obituary WHERE id = %s", (obituary_id,))
        row = cur.fetchone()
    if not row:
        raise HTTPException(status_code=404, detail="Obituary not found")
    return _row_to_dict(row)
