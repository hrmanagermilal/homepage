from fastapi import APIRouter, Depends, HTTPException
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all

router = APIRouter()


def _row_to_dict(row):
    if row is None:
        return None
    d = serialize(row)
    d["points"] = [p for p in d.get("points", "").split("\n") if p] if d.get("points") else []
    d["notice_button_external"] = bool(d.get("notice_button_external"))
    d["cta_external"] = bool(d.get("cta_external"))
    d["is_active"] = bool(d.get("is_active"))
    return d


@router.get("/ministry")
def get_ministry(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM ministry WHERE is_active = 1 ORDER BY `order`"
        )
        rows = cur.fetchall()
    return {"data": [_row_to_dict(r) for r in rows]}


@router.get("/ministry/{ministry_id}")
def get_ministry_by_id(ministry_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM ministry WHERE id = %s", (ministry_id,))
        row = cur.fetchone()
    if not row:
        raise HTTPException(status_code=404, detail="Ministry not found")
    return _row_to_dict(row)

