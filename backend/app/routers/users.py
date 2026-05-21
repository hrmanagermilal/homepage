from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..auth import require_role
from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/users", tags=["users"])

ALLOWED_ROLES = {"viewer", "manager"}


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
    _=Depends(require_role("manager")),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM users WHERE is_active = TRUE")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT id, username, email, role, created_at, last_login FROM users WHERE is_active = TRUE LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    return paginated(serialize_all(rows), total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    with db.cursor() as cur:
        cur.execute(
            "SELECT id, username, email, role, created_at, last_login FROM users WHERE id = %s",
            (item_id,),
        )
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(serialize(row))


@router.post("")
def create(body: dict, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    from ..auth import hash_password

    username = (body.get("username") or "").strip()
    email = (body.get("email") or "").strip()
    password = body.get("password") or ""
    role = body.get("role", "viewer")

    if not username or not email or not password:
        return error("username, email and password are required", "VALIDATION_ERROR", 400)
    if len(password) < 6:
        return error("Password must be at least 6 characters", "VALIDATION_ERROR", 400)
    if role not in ALLOWED_ROLES:
        return error(f"role must be one of {ALLOWED_ROLES}", "VALIDATION_ERROR", 400)

    with db.cursor() as cur:
        cur.execute("SELECT id FROM users WHERE username = %s OR email = %s", (username, email))
        if cur.fetchone():
            return error("Username or email already exists", "VALIDATION_ERROR", 400)
        cur.execute(
            "INSERT INTO users (username, email, password_hash, role) VALUES (%s, %s, %s, %s)",
            (username, email, hash_password(password), role),
        )
        new_id = cur.lastrowid
    with db.cursor() as cur:
        cur.execute(
            "SELECT id, username, email, role, created_at FROM users WHERE id = %s", (new_id,)
        )
        row = cur.fetchone()
    return success(serialize(row), status_code=201)


@router.put("/{item_id}")
def update(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM users WHERE id = %s AND is_active = TRUE", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE users SET email=%s, role=%s WHERE id=%s",
            (body.get("email"), body.get("role"), item_id),
        )
    with db.cursor() as cur:
        cur.execute(
            "SELECT id, username, email, role, created_at FROM users WHERE id = %s", (item_id,)
        )
        row = cur.fetchone()
    return success(serialize(row))


@router.put("/{item_id}/password")
def change_password(item_id: int, body: dict, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    from ..auth import hash_password

    password = body.get("password") or ""
    if len(password) < 6:
        return error("Password must be at least 6 characters", "VALIDATION_ERROR", 400)
    with db.cursor() as cur:
        cur.execute("SELECT id FROM users WHERE id = %s AND is_active = TRUE", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute(
            "UPDATE users SET password_hash = %s WHERE id = %s",
            (hash_password(password), item_id),
        )
    return success(None, "Password updated")


@router.delete("/{item_id}")
def delete(item_id: int, db: Connection = Depends(get_db), _=Depends(require_role("manager"))):
    with db.cursor() as cur:
        cur.execute("SELECT id FROM users WHERE id = %s AND is_active = TRUE", (item_id,))
        if not cur.fetchone():
            return error("Not found", "NOT_FOUND", 404)
        cur.execute("UPDATE users SET is_active = FALSE WHERE id = %s", (item_id,))
    return success(None, "Deleted successfully")
