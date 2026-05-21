from fastapi import APIRouter, Depends, Request
from pymysql.connections import Connection

from ..database import get_db
from ..response import success

router = APIRouter(prefix="/track", tags=["tracking"])


@router.post("/pageview")
def track_pageview(body: dict, request: Request, db: Connection = Depends(get_db)):
    page_path = (body.get("page_path") or "/").strip()
    user_agent = request.headers.get("user-agent", "")
    ip = request.headers.get("x-forwarded-for", request.client.host if request.client else "")
    # Take first IP if comma-separated
    ip = ip.split(",")[0].strip()

    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO page_views (page_path, ip_address, user_agent) VALUES (%s, %s, %s)",
            (page_path, ip, user_agent),
        )
    return success(None, "Tracked")
