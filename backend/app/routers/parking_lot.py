from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/parking-lot", tags=["parking-lot"])


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM parking_lot WHERE is_active = 1 ORDER BY sort_order"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM parking_lot WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))
