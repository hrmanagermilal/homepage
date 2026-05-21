from fastapi import APIRouter, Depends, Query
from pymysql.connections import Connection

from ..database import get_db, serialize_all
from ..response import success

router = APIRouter(prefix="/analytics", tags=["analytics"])


@router.get("/pages")
def page_stats(
    days: int = Query(30, ge=1, le=365),
    db: Connection = Depends(get_db),
):
    with db.cursor() as cur:
        cur.execute(
            """SELECT page_path, COUNT(*) as views
               FROM page_views
               WHERE created_at >= DATE_SUB(NOW(), INTERVAL %s DAY)
               GROUP BY page_path
               ORDER BY views DESC
               LIMIT 50""",
            (days,),
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/devices")
def device_stats(
    days: int = Query(30, ge=1, le=365),
    db: Connection = Depends(get_db),
):
    with db.cursor() as cur:
        cur.execute(
            """SELECT
                 CASE
                   WHEN user_agent LIKE '%Mobile%' THEN 'mobile'
                   WHEN user_agent LIKE '%Tablet%' THEN 'tablet'
                   ELSE 'desktop'
                 END AS device_type,
                 COUNT(*) as views
               FROM page_views
               WHERE created_at >= DATE_SUB(NOW(), INTERVAL %s DAY)
               GROUP BY device_type""",
            (days,),
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/browsers")
def browser_stats(
    days: int = Query(30, ge=1, le=365),
    db: Connection = Depends(get_db),
):
    with db.cursor() as cur:
        cur.execute(
            """SELECT
                 CASE
                   WHEN user_agent LIKE '%Chrome%' AND user_agent NOT LIKE '%Edg%' THEN 'Chrome'
                   WHEN user_agent LIKE '%Firefox%' THEN 'Firefox'
                   WHEN user_agent LIKE '%Safari%' AND user_agent NOT LIKE '%Chrome%' THEN 'Safari'
                   WHEN user_agent LIKE '%Edg%' THEN 'Edge'
                   ELSE 'Other'
                 END AS browser,
                 COUNT(*) as views
               FROM page_views
               WHERE created_at >= DATE_SUB(NOW(), INTERVAL %s DAY)
               GROUP BY browser""",
            (days,),
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))


@router.get("/recent")
def recent_views(
    limit: int = Query(20, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM page_views ORDER BY created_at DESC LIMIT %s",
            (limit,),
        )
        rows = cur.fetchall()
    return success(serialize_all(rows))
