import "./css/MinistrySubSection.css";

export default function MinistrySubSection({
  title,
  subtitle,
  image,
  description,
  points = [],
  noticeTitle = "4월 순모임 교재 공유드립니다.",
  noticeDescription = "PDF파일을 다운 받으셔서 순모임에 활용하세요.",
  noticeButtonLabel = "PDF 다운로드",
  noticeButtonHref = "#",
  noticeButtonExternal = false,
  ctaLabel,
  ctaHref = "#",
  ctaExternal = false,
}) {
  return (
    <section className="ministry-subsection">
      <div className="wrap">
        <div className="ministry-subsection__hero">
          <div className="ministry-subsection__hero-bg" aria-hidden="true">
            <img src={image} alt="" />
            <div className="ministry-subsection__hero-overlay" />
          </div>
          <div className="ministry-subsection__hero-cont">
            <p className="ministry-subsection__subtitle">{subtitle}</p>
            <h3 className="ministry-subsection__title">{title}</h3>
          </div>
        </div>
      </div>

      <div className="wrap-narrow">
        <div className="ministry-subsection__content">
          <p className="ministry-subsection__desc">{description}</p>
          {points.length > 0 ? (
            <ul className="ministry-subsection__list">
              {points.map((point) => (
                <li key={point} className="ministry-subsection__list-item">
                  {point}
                </li>
              ))}
            </ul>
          ) : null}
        </div>

        {ctaLabel ? (
          <a
            className="btn-basic-big btn-basic-big--trans ministry-subsection__cta"
            href={ctaHref}
            {...(ctaExternal ? { target: "_blank", rel: "noopener noreferrer" } : {})}
          >
            <span>{ctaLabel}</span>
          </a>
        ) : null}

        <div className="ministry-subsection__notice">
          <div className="ministry-subsection__notice-text">
            <p className="ministry-subsection__notice-title">{noticeTitle}</p>
            <p className="ministry-subsection__notice-desc">{noticeDescription}</p>
          </div>
          <a
            className="btn-basic-big btn-basic-big--trans ministry-subsection__notice-btn"
            href={noticeButtonHref}
            {...(noticeButtonExternal ? { target: "_blank", rel: "noopener noreferrer" } : {})}
          >
            <i aria-hidden="true" />
            <span>{noticeButtonLabel}</span>
          </a>
        </div>
      </div>
    </section>
  );
}
