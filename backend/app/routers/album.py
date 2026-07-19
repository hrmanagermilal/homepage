from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import error, paginated, success

router = APIRouter(prefix="/albums", tags=["albums"])


def _row_to_dict(row):
    if row is None:
        return None
    d = serialize(row)
    d["is_active"] = bool(d.get("is_active"))
    if d.get("date"):
        d["date"] = d["date"].strftime("%Y. %m. %d") if hasattr(d["date"], "strftime") else str(d["date"])
    return d


def _get_album_images(album_id: int, db: Connection) -> list[dict]:
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM album_images WHERE album_id = %s ORDER BY sort_order ASC, id ASC",
            (album_id,),
        )
        rows = cur.fetchall()
    return serialize_all(rows)


def _attach_images(album: dict, db: Connection) -> dict:
    album["images"] = _get_album_images(album["id"], db)
    return album


@router.get("")
def get_albums(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=200),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM album")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT * FROM album WHERE is_active = 1 ORDER BY date DESC, id DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    data = [_attach_images(_row_to_dict(r), db) for r in rows]
    return paginated(data, total, page, limit)


@router.get("/{album_id}")
def get_album_by_id(album_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM album WHERE id = %s AND is_active = 1", (album_id,))
        row = cur.fetchone()
    if not row:
        return error("Album not found", "NOT_FOUND", 404)
    return success(_attach_images(_row_to_dict(row), db))


@router.get("/{album_id}/images")
def get_album_images(album_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM album WHERE id = %s AND is_active = 1", (album_id,))
        row = cur.fetchone()
    if not row:
        return error("Album not found", "NOT_FOUND", 404)
    return success(_get_album_images(album_id, db))


@router.get("/album")
def get_album_legacy(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=200),
    db: Connection = Depends(get_db),
):
    return get_albums(page=page, limit=limit, db=db)


@router.get("/album/{album_id}")
def get_album_by_id_legacy(album_id: int, db: Connection = Depends(get_db)):
    return get_album_by_id(album_id=album_id, db=db)
