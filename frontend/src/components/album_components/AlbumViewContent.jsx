export default function AlbumViewContent({ title, content }) {
  return (
    <div className="board-view__inr">
      <h3 data-heading="5xl" dangerouslySetInnerHTML={{ __html: title }} />
      <div className="board-view__con">
        <p dangerouslySetInnerHTML={{ __html: content }} />
      </div>
    </div>
  );
}
