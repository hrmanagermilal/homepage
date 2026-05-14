const DEFAULT_THUMBS = [
  "/images/main/youtube-thumb-01.jpg",
  "/images/main/youtube-thumb-03.jpg",
  "/images/main/youtube-thumb-02.jpg",
];

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
  live = false,
  className = "",
  index = 0,
}) {
  return (
    <a
      className={`youtube-card${live ? " youtube-card--live" : ""}${className}`.trim()}
      href={url || "#"}
      target="_blank"
      rel="noopener noreferrer"
      data-ani="top"
    >
      <div className="youtube-card__thumb">
        <img src={getYoutubeThumb(url, index)} alt={title || "설교 썸네일"} />
      </div>
      <div className="youtube-card__gradient"></div>
      {live ? (
        <div className="youtube-card__live-badge" aria-label="라이브 방송 중">
          <span className="youtube-card__live-dot" aria-hidden="true"></span>
          <span className="youtube-card__live-text">LIVE</span>
        </div>
      ) : null}
      <div className="youtube-card__label">
        <h3>{title || "제목 없음"}{preacher ? <><br />{preacher}</> : null}</h3>
      </div>
    </a>
  );
}

export default VideoCard;
