export default function NoticeViewContent({ title, author, date, views, content, image, link, link_text }) {
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
        {image && (
          <div className="notice-view__image-wrap">
            <img src={image} alt={title} className="notice-view__image" />
          </div>
        )}
        {link && link_text && (
          <div className="notice-view__link-wrap">
            <a href={link} className="notice-view__link-btn" target="_blank" rel="noopener noreferrer">
              {link_text}
            </a>
          </div>
        )}
      </div>
    </div>
  );
}
