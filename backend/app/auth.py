import os
from datetime import datetime, timezone
from typing import Any

from fastapi import HTTPException, Security
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from passlib.context import CryptContext

SECRET_KEY: str = os.getenv("JWT_SECRET", "change-this-secret")
ALGORITHM = "HS256"
EXPIRY: int = int(os.getenv("JWT_EXPIRY", "604800"))

ROLE_LEVELS: dict[str, int] = {
    "viewer": 1,
    "editor": 2,
    "manager": 2,
    "admin": 3,
}

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
bearer_scheme = HTTPBearer(auto_error=False)


def verify_password(plain: str, hashed: str) -> bool:
    return pwd_context.verify(plain, hashed)


def hash_password(plain: str) -> str:
    return pwd_context.hash(plain)


def create_token(data: dict[str, Any]) -> str:
    now = int(datetime.now(timezone.utc).timestamp())
    payload = {**data, "iat": now, "exp": now + EXPIRY}
    return jwt.encode(payload, SECRET_KEY, algorithm=ALGORITHM)


def decode_token(token: str) -> dict[str, Any]:
    try:
        return jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
    except JWTError:
        raise HTTPException(status_code=401, detail="Invalid or expired token")


def get_current_user(
    credentials: HTTPAuthorizationCredentials | None = Security(bearer_scheme),
) -> dict[str, Any]:
    if not credentials:
        raise HTTPException(status_code=401, detail="Authentication required")
    return decode_token(credentials.credentials)


def require_role(min_role: str):
    """Return a FastAPI dependency that enforces a minimum role level."""

    def check(
        credentials: HTTPAuthorizationCredentials | None = Security(bearer_scheme),
    ) -> dict[str, Any]:
        if not credentials:
            raise HTTPException(status_code=401, detail="Authentication required")
        user = decode_token(credentials.credentials)
        if ROLE_LEVELS.get(user.get("role", ""), 0) < ROLE_LEVELS.get(min_role, 999):
            raise HTTPException(status_code=403, detail="Insufficient permissions")
        return user

    return check
