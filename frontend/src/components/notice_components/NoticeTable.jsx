export default function NoticeTable({ notices = [], onRowClick = () => {} }) {
  return (
    <div className="notice-table-wrapper">
      <table className="notice-table">
        <thead className="notice-table__head">
          <tr>
            <th className="notice-table__cell notice-table__cell--number">#</th>
            <th className="notice-table__cell notice-table__cell--title">제목</th>
            <th className="notice-table__cell notice-table__cell--author">작성자</th>
            <th className="notice-table__cell notice-table__cell--date">작성일</th>
            <th className="notice-table__cell notice-table__cell--views">조회</th>
          </tr>
        </thead>
        <tbody className="notice-table__body">
          {notices.length > 0 ? (
            notices.map((notice, index) => (
              <tr
                key={notice.id}
                className="notice-table__row"
                onClick={() => onRowClick(notice.id)}
                role="button"
                tabIndex={0}
                onKeyDown={(e) => {
                  if (e.key === "Enter") onRowClick(notice.id);
                }}
              >
                <td className="notice-table__cell notice-table__cell--number">{index + 1}</td>
                <td className="notice-table__cell notice-table__cell--title notice-table__row-title">{notice.title}</td>
                <td className="notice-table__cell notice-table__cell--author">{notice.author}</td>
                <td className="notice-table__cell notice-table__cell--date">{notice.date}</td>
                <td className="notice-table__cell notice-table__cell--views">{notice.views}</td>
              </tr>
            ))
          ) : (
            <tr className="notice-table__empty">
              <td colSpan="5" className="notice-table__cell">공지사항이 없습니다.</td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
