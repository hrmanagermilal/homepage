from fastapi import APIRouter, Depends
from pymysql.connections import Connection

from ..auth import verify_password, create_token, get_current_user
from ..database import get_db, serialize
from ..response import success, error

router = APIRouter(prefix="/auth", tags=["auth"])


@router.post("/login")
def login(body: dict, db: Connection = Depends(get_db)):
    username = (body.get("username") or "").strip()
    password = body.get("password") or ""
    if not username or not password:
        return error("Username and password are required", "VALIDATION_ERROR", 400)

    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM users WHERE username = %s AND is_active = TRUE LIMIT 1",
            (username,),
        )
        user = cur.fetchone()

    if not user or not verify_password(password, user["password_hash"]):
        return error("Invalid username or password", "UNAUTHORIZED", 401)

    with db.cursor() as cur:
        cur.execute("UPDATE users SET last_login = NOW() WHERE id = %s", (user["id"],))

    token = create_token(
        {"id": user["id"], "username": user["username"], "role": user["role"]}
    )
    user_data = serialize(user)
    user_data.pop("password_hash", None)
    return success({"token": token, "user": user_data})


@router.post("/logout")
def logout():
    return success(None, "Logged out successfully")


@router.get("/me")
def me(current_user: dict = Depends(get_current_user), db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(
            "SELECT id, username, email, role, created_at, last_login FROM users WHERE id = %s",
            (current_user["id"],),
        )
        user = cur.fetchone()
    if not user:
        return error("User not found", "NOT_FOUND", 404)
    return success(serialize(user))
