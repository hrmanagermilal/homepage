from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db, serialize
from ..response import success

router = APIRouter(prefix="/pastor-introduction", tags=["pastor-introduction"])


@router.get("")
def get_pastor_introduction(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM pastor_introduction WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
        )
        row = cur.fetchone()
    return success(serialize(row))
