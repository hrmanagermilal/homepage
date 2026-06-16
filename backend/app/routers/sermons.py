import os
import re
import httpx
from fastapi import APIRouter, Depends, Query, HTTPException
from pydantic import BaseModel
from pymysql.connections import Connection
from datetime import date, datetime, timedelta

from ..database import get_db, serialize, serialize_all
from ..response import success, error, paginated

router = APIRouter(prefix="/sermons", tags=["sermons"])

_auto_register_last_run: datetime | None = None
_AUTO_REGISTER_COOLDOWN = timedelta(minutes=10)

_SELECT = """
    SELECT s.*, sc.title AS category_title, sc.image AS category_image
    FROM sermons s
    LEFT JOIN sermon_categories sc ON s.category_id = sc.id
"""


# ── Data Models ───────────────────────────────────────────────────────────
class SermonCreate(BaseModel):
    title: str
    category_id: int | None = None
    youtube_url: str | None = None
    description: str | None = None
    preacher: str | None = None
    sermon_date: date | None = None
    thumbnail: str | None = None


class SermonUpdate(BaseModel):
    title: str | None = None
    category_id: int | None = None
    youtube_url: str | None = None
    description: str | None = None
    preacher: str | None = None
    sermon_date: date | None = None
    thumbnail: str | None = None


# ── Helper Functions ──────────────────────────────────────────────────────
def extract_video_id(url: str) -> str | None:
    """Extract YouTube video ID from various URL formats."""
    patterns = [
        r'(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})',
        r'(?:youtube\.com\/)?([a-zA-Z0-9_-]{11})',
    ]
    for pattern in patterns:
        match = re.search(pattern, url)
        if match:
            return match.group(1)
    return None


async def check_youtube_is_live(video_id: str) -> bool:
    """
    Check if a YouTube video is currently live.
    Returns True if LIVE, False if NOT live or error.
    """
    api_key = os.getenv("YOUTUBE_API_KEY", "")
    if not api_key:
        # If no API key, assume it's not live (fail-safe)
        return False
    
    try:
        url = "https://www.googleapis.com/youtube/v3/videos"
        params = {
            "id": video_id,
            "part": "liveStreamingDetails,status",
            "key": api_key
        }
        
        async with httpx.AsyncClient() as client:
            response = await client.get(url, params=params, timeout=10)
            response.raise_for_status()
        
        data = response.json()
        if not data.get("items"):
            return False
        
        video = data["items"][0]
        live_details = video.get("liveStreamingDetails", {})
        
        # Check if currently live (has actualStartTime but no actualEndTime)
        is_live = bool(
            live_details.get("actualStartTime") and
            not live_details.get("actualEndTime")
        )
        return is_live
        
    except Exception:
        # On error, assume not live (fail-safe)
        return False


@router.get("")
async def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    await auto_register_sermons(db)

    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM sermons")
        total = cur.fetchone()["total"]
        cur.execute(
            _SELECT + " ORDER BY s.sermon_date DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    
    # Update live status for sermons marked as potentially live (is_live=0)
    updated_rows = []
    for row in rows:
        if row.get("is_live") == 0 and row.get("youtube_id"):
            # Check if still live and update database (is_live=0 means LIVE, need to verify)
            is_live = await check_youtube_is_live(row["youtube_id"])
            if not is_live:
                # Video is NOT live anymore, update to is_live=1 (NON-LIVE)
                with db.cursor() as cur:
                    cur.execute("UPDATE sermons SET is_live = 1 WHERE id = %s", (row["id"],))
                    db.commit()
                row["is_live"] = 1
        updated_rows.append(row)
    
    return paginated(serialize_all(updated_rows), total, page, limit)


@router.get("/{item_id}")
async def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute(_SELECT + " WHERE s.id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    
    # Update live status if marked as potentially live (is_live=0 means LIVE, needs verification)
    # If is_live=1, it's already verified as NON-LIVE, skip check
    if row.get("is_live") == 0 and row.get("youtube_id"):
        is_live = await check_youtube_is_live(row["youtube_id"])
        if not is_live:
            # Video is NOT live, update database to is_live=1 (NON-LIVE)
            with db.cursor() as cur:
                cur.execute("UPDATE sermons SET is_live = 1 WHERE id = %s", (item_id,))
                db.commit()
            row["is_live"] = 1
    
    return success(serialize(row))


@router.post("")
async def create_sermon(payload: SermonCreate, db: Connection = Depends(get_db)):
    """
    Create a new sermon with is_live=0 by default (assume LIVE).
    Live status will be verified on first GET request.
    Logic: is_live=0 means LIVE, is_live=1 means NON-LIVE
    """
    try:
        youtube_id = None
        
        # Validate YouTube URL if provided
        if payload.youtube_url:
            youtube_id = extract_video_id(payload.youtube_url)
            if not youtube_id:
                return error(
                    "Invalid YouTube URL format",
                    "INVALID_YOUTUBE_URL",
                    400
                )
        
        # Insert sermon with is_live=0 (default assume LIVE)
        # Live status will be checked and updated on GET requests
        with db.cursor() as cur:
            cur.execute("""
                INSERT INTO sermons (
                    title, category_id, youtube_url, youtube_id, 
                    description, preacher, sermon_date, thumbnail, is_live
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 0)
            """, (
                payload.title,
                payload.category_id,
                payload.youtube_url,
                youtube_id,
                payload.description,
                payload.preacher,
                payload.sermon_date,
                payload.thumbnail,
            ))
            db.commit()
            sermon_id = cur.lastrowid
        
        # Fetch and return created sermon
        with db.cursor() as cur:
            cur.execute(_SELECT + " WHERE s.id = %s", (sermon_id,))
            row = cur.fetchone()
        
        return success(serialize(row), "Sermon created successfully (live status will be verified on retrieval)")
        
    except Exception as e:
        db.rollback()
        return error(str(e), "CREATION_ERROR", 500)


@router.put("/{item_id}")
async def update_sermon(
    item_id: int,
    payload: SermonUpdate,
    db: Connection = Depends(get_db)
):
    """
    Update an existing sermon.
    If youtube_url is changed, resets is_live to 1 for re-verification on next GET.
    """
    try:
        # Check if sermon exists
        with db.cursor() as cur:
            cur.execute("SELECT * FROM sermons WHERE id = %s", (item_id,))
            existing = cur.fetchone()
        
        if not existing:
            return error("Sermon not found", "NOT_FOUND", 404)
        
        # Build update fields
        updates = {}
        if payload.title is not None:
            updates["title"] = payload.title
        if payload.category_id is not None:
            updates["category_id"] = payload.category_id
        if payload.description is not None:
            updates["description"] = payload.description
        if payload.preacher is not None:
            updates["preacher"] = payload.preacher
        if payload.sermon_date is not None:
            updates["sermon_date"] = payload.sermon_date
        if payload.thumbnail is not None:
            updates["thumbnail"] = payload.thumbnail
        
        # Handle YouTube URL update
        if payload.youtube_url is not None:
            youtube_id = extract_video_id(payload.youtube_url)
            if not youtube_id:
                return error(
                    "Invalid YouTube URL format",
                    "INVALID_YOUTUBE_URL",
                    400
                )
            
            updates["youtube_url"] = payload.youtube_url
            updates["youtube_id"] = youtube_id
            # Reset is_live to 0 (LIVE) so status is re-verified on next GET
            updates["is_live"] = 0
        
        # Update sermon
        if updates:
            set_clause = ", ".join([f"{key} = %s" for key in updates.keys()])
            values = list(updates.values()) + [item_id]
            
            with db.cursor() as cur:
                cur.execute(f"UPDATE sermons SET {set_clause} WHERE id = %s", values)
                db.commit()
        
        # Fetch and return updated sermon
        with db.cursor() as cur:
            cur.execute(_SELECT + " WHERE s.id = %s", (item_id,))
            row = cur.fetchone()
        
        return success(serialize(row), "Sermon updated successfully")
        
    except Exception as e:
        db.rollback()
        return error(str(e), "UPDATE_ERROR", 500)


@router.post("/auto-register")
async def auto_register_sermons(db: Connection = Depends(get_db)):
    """
    Fetch the latest 5 videos from the church YouTube playlist and
    automatically register any that are not yet in the sermons table.

    Playlist: https://www.youtube.com/playlist?list=PLNJ54FCvyg8M63ptgyDvGYnzt768d19Ky

    Runs at most once every 10 minutes; subsequent calls within the cooldown
    return immediately without hitting the YouTube API.
    """
    global _auto_register_last_run
    now = datetime.utcnow()
    if _auto_register_last_run and (now - _auto_register_last_run) < _AUTO_REGISTER_COOLDOWN:
        remaining = int((_AUTO_REGISTER_COOLDOWN - (now - _auto_register_last_run)).total_seconds())
        return success(None, f"Auto-register skipped (cooldown: {remaining}s remaining)")

    PLAYLIST_ID = "PLNJ54FCvyg8M63ptgyDvGYnzt768d19Ky"
    api_key = os.getenv("YOUTUBE_API_KEY", "")
    category_id = 1  # Default category ID for auto-registered sermons
    if not api_key:
        return error("YouTube API key not configured", "NO_API_KEY", 500)

    try:
        async with httpx.AsyncClient(timeout=15) as client:
            # Step 1: get latest 5 video IDs from playlist
            pl_resp = await client.get(
                "https://www.googleapis.com/youtube/v3/playlistItems",
                params={
                    "playlistId": PLAYLIST_ID,
                    "part": "contentDetails",
                    "maxResults": 5,
                    "key": api_key,
                },
            )
            pl_resp.raise_for_status()
            pl_data = pl_resp.json()

            items = pl_data.get("items", [])
            if not items:
                return success({"registered": [], "skipped": []}, "No videos found in playlist")

            video_ids = [item["contentDetails"]["videoId"] for item in items]

            # Step 2: fetch video details (title, description, thumbnail, publishedAt)
            vid_resp = await client.get(
                "https://www.googleapis.com/youtube/v3/videos",
                params={
                    "id": ",".join(video_ids),
                    "part": "snippet",
                    "key": api_key,
                },
            )
            vid_resp.raise_for_status()
            vid_data = vid_resp.json()

    except httpx.HTTPStatusError as exc:
        return error(f"YouTube API error: {exc.response.status_code}", "YOUTUBE_API_ERROR", 502)
    except Exception as exc:
        return error(str(exc), "FETCH_ERROR", 500)

    registered = []
    skipped = []

    for video in vid_data.get("items", []):
        video_id = video["id"]
        snippet = video.get("snippet", {})
        title = snippet.get("title", "")
        description = snippet.get("description", "")
        published_at = snippet.get("publishedAt", "")
        thumbnails = snippet.get("thumbnails", {})
        thumbnail = (
            thumbnails.get("maxres", {}).get("url")
            or thumbnails.get("high", {}).get("url")
            or thumbnails.get("default", {}).get("url")
        )
        youtube_url = f"https://www.youtube.com/watch?v={video_id}"

        # Derive sermon_date from publishedAt (YYYY-MM-DD)
        sermon_date = published_at[:10] if published_at else None

        # Extract preacher from description (following "설교: ")
        preacher_match = re.search(r"설교[:\s]+([^\n]+)", description)
        preacher = preacher_match.group(1).strip() if preacher_match else None

        # Skip if already registered
        with db.cursor() as cur:
            cur.execute("SELECT id FROM sermons WHERE youtube_id = %s", (video_id,))
            existing = cur.fetchone()

        if existing:
            skipped.append({"youtube_id": video_id, "title": title})
            continue

        try:
            with db.cursor() as cur:
                cur.execute(
                    """
                    INSERT INTO sermons (
                        title, category_id, youtube_url, youtube_id,
                        description, preacher, sermon_date, thumbnail, is_live
                    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 1)
                    """,
                    (title, category_id, youtube_url, video_id, description, preacher, sermon_date, thumbnail),
                )
                db.commit()
                sermon_id = cur.lastrowid
            registered.append({"id": sermon_id, "youtube_id": video_id, "title": title})
        except Exception as exc:
            db.rollback()
            return error(str(exc), "INSERT_ERROR", 500)

    _auto_register_last_run = now
    return success(
        {"registered": registered, "skipped": skipped},
        f"{len(registered)} sermon(s) registered, {len(skipped)} already existed",
    )


@router.delete("/{item_id}")
def delete_sermon(item_id: int, db: Connection = Depends(get_db)):
    """Delete a sermon."""
    try:
        # Check if sermon exists
        with db.cursor() as cur:
            cur.execute("SELECT * FROM sermons WHERE id = %s", (item_id,))
            if not cur.fetchone():
                return error("Sermon not found", "NOT_FOUND", 404)
            
            cur.execute("DELETE FROM sermons WHERE id = %s", (item_id,))
            db.commit()
        
        return success(None, "Sermon deleted successfully")
        
    except Exception as e:
        db.rollback()
        return error(str(e), "DELETE_ERROR", 500)
