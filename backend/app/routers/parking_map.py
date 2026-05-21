from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db, serialize
from ..response import success, error

router = APIRouter(prefix="/parking-map", tags=["parking-map"])


@router.get("")
def get_active(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM parking_map WHERE is_active = 1 ORDER BY id DESC LIMIT 1"
        )
        row = cur.fetchone()
    return success(serialize(row))
