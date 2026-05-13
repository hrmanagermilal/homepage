export default function NoticeViewNavigation({
  onPrevClick,
  onNextClick,
  onListClick,
  hasPrev = false,
  hasNext = false,
}) {
  return (
    <div className="view-btn-wrap">
      <button
        className="btn-navigation btn-prev"
        onClick={onPrevClick}
        disabled={!hasPrev}
        style={{ opacity: hasPrev ? 1 : 0.5, cursor: hasPrev ? "pointer" : "not-allowed" }}
      >
        이전글
      </button>
      <button
        className="btn-basic-big btn-list"
        onClick={onListClick}
        type="button"
      >
        목록으로
      </button>
      <button
        className="btn-navigation btn-next"
        onClick={onNextClick}
        disabled={!hasNext}
        style={{ opacity: hasNext ? 1 : 0.5, cursor: hasNext ? "pointer" : "not-allowed" }}
      >
        다음글
      </button>
    </div>
  );
}
