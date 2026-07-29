from fastapi import APIRouter, Depends, HTTPException
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter()

@router.get("/nextgen")
def get_nextgen(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM departments WHERE department_type = 'nextgen' ORDER BY sort_order"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/nextgen/{item_id}")
def get_nextgen_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM departments WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize_all([row])[0])

