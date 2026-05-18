export default function BulletinTable({ bulletins = [], onRowClick = () => {} }) {
  return (
    <div className="notice-table-wrapper">
      <table className="notice-table">
        <thead className="notice-table__head">
          <tr>
            <th className="notice-table__cell notice-table__cell--number">#</th>
            <th className="notice-table__cell notice-table__cell--title">제목</th>
            <th className="notice-table__cell notice-table__cell--date">주차</th>
            <th className="notice-table__cell notice-table__cell--date">날짜</th>
          </tr>
        </thead>
        <tbody className="notice-table__body">
          {bulletins.length > 0 ? (
            bulletins.map((bulletin, index) => (
              <tr
                key={bulletin.id}
                className="notice-table__row"
                onClick={() => onRowClick(bulletin.id)}
                role="button"
                tabIndex={0}
                onKeyDown={(e) => {
                  if (e.key === "Enter") onRowClick(bulletin.id);
                }}
              >
                <td className="notice-table__cell notice-table__cell--number">{index + 1}</td>
                <td className="notice-table__cell notice-table__cell--title notice-table__row-title">{bulletin.title}</td>
                <td className="notice-table__cell notice-table__cell--date">
                  {bulletin.year && bulletin.week_number ? `${bulletin.year}년 ${bulletin.week_number}주` : "—"}
                </td>
                <td className="notice-table__cell notice-table__cell--date">
                  {bulletin.created_at ? String(bulletin.created_at).slice(0, 10) : "—"}
                </td>
              </tr>
            ))
          ) : (
            <tr className="notice-table__empty">
              <td colSpan="4" className="notice-table__cell">주보가 없습니다.</td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
