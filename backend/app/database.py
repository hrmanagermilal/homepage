import os
import pymysql
from dbutils.pooled_db import PooledDB

_pool: PooledDB | None = None


def init_db() -> None:
    global _pool
    _pool = PooledDB(
        creator=pymysql,
        host=os.getenv("DB_HOST", "db"),
        user=os.getenv("DB_USER", "root"),
        password=os.getenv("DB_PASSWORD", "milal_root_2024"),
        database=os.getenv("DB_NAME", "milal_homepage"),
        port=int(os.getenv("DB_PORT", "3306")),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        maxconnections=20,
        mincached=2,
        autocommit=True,
    )


def get_db():
    conn = _pool.connection()
    try:
        yield conn
    finally:
        conn.close()


def serialize(row: dict | None) -> dict | None:
    if row is None:
        return None
    out = {}
    for k, v in row.items():
        if hasattr(v, "isoformat"):
            out[k] = v.isoformat()
        elif isinstance(v, bytes):
            out[k] = v.decode("utf-8")
        else:
            out[k] = v
    return out


def serialize_all(rows) -> list[dict]:
    return [serialize(r) for r in rows]
