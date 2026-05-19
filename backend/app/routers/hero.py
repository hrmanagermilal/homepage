from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/hero", tags=["hero"])


@router.get("")
def get_hero(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM hero_background_images ORDER BY `order` ASC"
        )
        backgrounds = cur.fetchall()
        cur.execute(
            "SELECT * FROM hero_front_images ORDER BY uploaded_at ASC"
        )
        fronts = cur.fetchall()

    data = {}
    data["background_images"] = serialize_all(backgrounds)
    data["front_images"] = serialize_all(fronts)
    return success(data)


@router.get("/background-images")
def get_background_images(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM hero_background_images ORDER BY `order` ASC"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))
