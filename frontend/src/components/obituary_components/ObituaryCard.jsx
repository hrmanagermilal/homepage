export default function ObituaryCard({ id, title, description, date, href = "#" }) {
  return (
    <li data-ani="top">
      <a className="obituary-card" href={`/news/obituary/${id}`}>
        <div className="obituary-card__top">
          <div className="obituary-card__head">
            <i className="obituary-card__icon" aria-hidden="true" />
            <h4 className="obituary-card__title" data-heading="xl" dangerouslySetInnerHTML={{ __html: title }} />
          </div>
          <hr className="obituary-card__line" />
          <p className="obituary-card__desc">{description}</p>
        </div>
        <p className="obituary-card__date">{date}</p>
      </a>
    </li>
  );
}
