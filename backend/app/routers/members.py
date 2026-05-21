import os
import shutil
import uuid

from fastapi import APIRouter, Depends, File, UploadFile
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error

router = APIRouter(prefix="/members", tags=["members"])

UPLOAD_DIR = os.getenv("UPLOADS_PATH", "./uploads") + "/members"


@router.get("")
def get_all(db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM members WHERE is_active = TRUE ORDER BY sort_order, created_at"
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM members WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.get("/{role}/role")
def get_by_role(role: str, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM members WHERE category = %s AND is_active = TRUE ORDER BY sort_order",
            (role,),
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    name = (body.get("name") or "").strip()
    if not name:
        return error("name is required", "VALIDATION_ERROR", 400)
    with db.cursor() as cur:
        cur.execute(
            """INSERT INTO members (name, title, category, biography, picture, is_active, sort_order)
               VALUES (%s, %s, %s, %s, %s, %s, %s)""",
            (
                name,
                body.get("title"),
                body.get("category"),
                body.get("biography"),
                body.get("picture"),
                body.get("is_active", True),
                body.get("sort_order", 0),
            ),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute("SELECT * FROM members WHERE id = %s", (new_id,))
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("editor"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM members WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            """UPDATE members SET name=%s, title=%s, category=%s, biography=%s,
               picture=%s, is_active=%s, sort_order=%s WHERE id=%s""",
            (
                body.get("name"),
                body.get("title"),
                body.get("category"),
                body.get("biography"),
                body.get("picture"),
                body.get("is_active"),
                body.get("sort_order"),
                item_id,
            ),
        )
    with db.cursor() as cur:
        cur.execute("SELECT * FROM members WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM members WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("UPDATE members SET is_active = FALSE WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")


@router.post("/{item_id}/picture")
async def upload_picture(
    item_id: int,
    file: UploadFile = File(...),
    db: Connection = Depends(get_db),
    _=Depends(require_role("editor")),
):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM members WHERE id = %s", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)

    ext = os.path.splitext(file.filename or "")[1].lower() or ".jpg"
    filename = f"{uuid.uuid4().hex}{ext}"
    os.makedirs(UPLOAD_DIR, exist_ok=True)
    dest = os.path.join(UPLOAD_DIR, filename)
    with open(dest, "wb") as f:
        shutil.copyfileobj(file.file, f)

    image_url = f"/uploads/members/{filename}"
    with db.cursor() as cur:
        cur.execute("UPDATE members SET picture = %s WHERE id = %s", (image_url, item_id))
    with db.cursor() as cur:
        cur.execute("SELECT * FROM members WHERE id = %s", (item_id,))
        row = cur.fetchone()
    return success(serialize(row))
