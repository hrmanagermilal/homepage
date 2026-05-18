from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/departments", tags=["departments"])


def _attach_announcements(dept: dict, db: Connection) -> dict:
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM department_announcements WHERE department_id = %s ORDER BY created_at DESC",
            (dept["id"],),
        )
        dept["announcements"] = serialize_all(cur.fetchall())
    return dept


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM departments ORDER BY department_type, `order`")
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM departments WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(_attach_announcements(serialize(row), db))


@router.get("/nextgen/list")
def get_nextgen(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM departments WHERE department_type = 'nextgen' ORDER BY `order`"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/ministry/list")
def get_ministry(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM departments WHERE department_type = 'ministry' ORDER BY `order`"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))
