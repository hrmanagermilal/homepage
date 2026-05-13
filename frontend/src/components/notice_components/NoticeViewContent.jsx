export default function NoticeViewContent({ title, author, date, views, content }) {
  return (
    <div className="board-view__inr">
      <h3 data-heading="5xl">{title}</h3>
      <div className="notice-view__meta">
        <span className="notice-view__meta-item">작성자: {author}</span>
        <span className="notice-view__meta-item">작성일: {date}</span>
        <span className="notice-view__meta-item">조회: {views}</span>
      </div>
      <div className="board-view__con">
        <p dangerouslySetInnerHTML={{ __html: content }} />
      </div>
    </div>
  );
}
