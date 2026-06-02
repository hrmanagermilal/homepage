import os
import httpx
from fastapi import APIRouter, HTTPException

router = APIRouter(tags=["youtube"])

YOUTUBE_API_KEY = os.getenv("YOUTUBE_API_KEY", "")

@router.get("/youtube/is-live/{video_id}")
async def check_youtube_live(video_id: str):
    """
    Check if a YouTube video is currently live using YouTube Data API v3.
    
    Returns:
        {
            "is_live": bool,
            "status": "live" | "ended" | "upcoming" | "unknown"
        }
    """
    if not YOUTUBE_API_KEY:
        raise HTTPException(
            status_code=500,
            detail="YouTube API key not configured"
        )
    
    try:
        url = "https://www.googleapis.com/youtube/v3/videos"
        params = {
            "id": video_id,
            "part": "liveStreamingDetails,status",
            "key": YOUTUBE_API_KEY
        }
        
        async with httpx.AsyncClient() as client:
            response = await client.get(url, params=params, timeout=10)
            response.raise_for_status()
            
        data = response.json()
        
        if not data.get("items"):
            return {"is_live": False, "status": "unknown"}
        
        video = data["items"][0]
        live_details = video.get("liveStreamingDetails", {})
        status = video.get("status", {}).get("uploadStatus", "unknown")
        
        # Check if currently live (has actualStartTime but no actualEndTime)
        is_live = bool(
            live_details.get("actualStartTime") and
            not live_details.get("actualEndTime")
        )
        
        # Determine status
        if is_live:
            status_label = "live"
        elif live_details.get("scheduledStartTime") and not live_details.get("actualStartTime"):
            status_label = "upcoming"
        elif live_details.get("actualEndTime"):
            status_label = "ended"
        else:
            status_label = "unknown"
        
        return {
            "is_live": is_live,
            "status": status_label
        }
        
    except httpx.RequestError as e:
        raise HTTPException(
            status_code=503,
            detail=f"Failed to reach YouTube API: {str(e)}"
        )
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Error checking live status: {str(e)}"
        )
