import { useEffect, useState } from "react";

const DEFAULT_THUMBS = [
  "/images/main/youtube-thumb-01.jpg",
  "/images/main/youtube-thumb-03.jpg",
  "/images/main/youtube-thumb-02.jpg",
];

function extractVideoId(url) {
  if (!url) return null;
  const match = url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{6,})/);
  return match?.[1] ?? null;
}

/**
 * Returns true if the YouTube URL is currently live.
 * Uses the hqdefault_live.jpg thumbnail trick:
 * YouTube returns a 120×90 placeholder when not live, full-size when live.
 */
function useLiveCheck(url) {
  const [isLive, setIsLive] = useState(false);

  useEffect(() => {
    const videoId = extractVideoId(url);
    if (!videoId) { setIsLive(false); return; }
    let cancelled = false;
    const img = new Image();
    img.onload = () => { if (!cancelled) setIsLive(img.naturalWidth > 120); };
    img.onerror = () => { if (!cancelled) setIsLive(false); };
    img.src = `https://img.youtube.com/vi/${videoId}/hqdefault_live.jpg`;
    return () => { cancelled = true; };
  }, [url]);

  return isLive;
}

function getYoutubeThumb(url, index) {
  if (!url) return DEFAULT_THUMBS[index % DEFAULT_THUMBS.length];
  const match = url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{6,})/);
  if (!match?.[1]) return DEFAULT_THUMBS[index % DEFAULT_THUMBS.length];
  return `https://img.youtube.com/vi/${match[1]}/hqdefault.jpg`;
}

/**
 * VideoCard component - renders a YouTube video card
 * @param {Object} props
 * @param {string} props.url - YouTube URL
 * @param {string} props.title - Video title
 * @param {string} props.preacher - Video subtitle/preacher info
 * @param {boolean} props.live - Whether the video is live
 * @param {string} props.className - Additional CSS classes
 * @param {number} props.index - Index for default thumbnail selection
 */
export function VideoCard({
  url,
  title,
  preacher,
  live = null,
  className = "",
  index = 0,
  thumbnail,
  hide_title = true,
}) {
  const isLive = useLiveCheck(url);
  const handleClick = () => {
    if (typeof window.gtag === "function") {
      window.gtag("event", "youtube_click", {
        event_category: "Video",
        event_label: title || url || "unknown",
        video_url: url || "",
      });
    }
  };

  return (
    <a
      className={`youtube-card${isLive ? " youtube-card--live" : ""}${className}`.trim()}
      href={url || "#"}
      target="_blank"
      rel="noopener noreferrer"
      data-ani="top"
      onClick={handleClick}
    >
      <div className="youtube-card__thumb">
        <img src={hide_title ? thumbnail : (thumbnail || getYoutubeThumb(url, index))} alt={title || "설교 썸네일"} />
      </div>
      {!hide_title && <div className="youtube-card__gradient"></div>}
      {isLive ? (
        <div className="youtube-card__live-badge" aria-label="라이브 방송 중">
          <span className="youtube-card__live-dot" aria-hidden="true"></span>
          <span className="youtube-card__live-text">LIVE</span>
        </div>
      ) : null}
      {!hide_title && (
        <div className="youtube-card__label">
          <h3>{title || "제목 없음"}{preacher ? <><br />{preacher}</> : null}</h3>
        </div>
      )}
    </a>
  );
}

export default VideoCard;
