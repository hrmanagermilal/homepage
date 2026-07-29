export default function AlbumViewNavigation({ onListClick }) {
  return (
    <div className="view-btn-wrap" style={{ display: "flex", justifyContent: "center" }}>
      <button
        className="btn-basic-big btn-list"
        onClick={onListClick}
        type="button"
      >
        목록으로
      </button>
    </div>
  );
}
