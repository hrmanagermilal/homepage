import "./css/NextGenDepartment.css";

export default function NextGenDepartment({
  headingTitle,
  headingSub,
  worshipLabel = "예배 안내",
  worshipTime,
  worshipLocation,
  pastorName,
  pastorEmail,
  pastorPhoto,
  kakaoLink,
  kakaoLabel,
  photoAlt,
  noticeTitle,
  noticeDescription,
  noticeButtonLabel,
  noticeButtonHref,
}) {
  return (
    <section className="next-gen">
      <div className="wrap-narrow">
        <div className="next-gen__head" data-ani="top">
          <h3 className="next-gen__head-title" data-heading="3xl">
            {headingTitle}
          </h3>
          <p className="next-gen__head-sub">{headingSub}</p>
        </div>

        <div className="next-gen__divider" data-ani="top" role="separator" />

        <div className="next-gen__worship" data-ani="top">
          <h4 className="next-gen__worship-label" data-heading="lg">
            {worshipLabel}
          </h4>
          <ul className="next-gen__worship-info">
            <li className="next-gen__worship-item">
              <img src="/images/sub/02-next-generation/icon-time.svg" alt="" aria-hidden="true" />
              <span>{worshipTime}</span>
            </li>
            <li className="next-gen__worship-item">
              <img src="/images/sub/02-next-generation/icon-location.svg" alt="" aria-hidden="true" />
              <span>{worshipLocation}</span>
            </li>
          </ul>
        </div>

        <div className="next-gen__pastor" data-ani="top">
          <div className="next-gen__pastor-photo">
            <img src={pastorPhoto || "/images/sub/02-next-generation/pastor-photo.jpg"} alt={photoAlt} />
          </div>
          <div className="next-gen__pastor-cont">
            <div className="next-gen__pastor-info">
              <h4 className="next-gen__pastor-name" data-heading="xl">
                {pastorName}
              </h4>
              <a href={`mailto:${pastorEmail}`} className="next-gen__pastor-email">
                <img className="next-gen__pastor-email-icon" src="/images/sub/02-next-generation/icon-mail.svg" alt="" />
                <span>{pastorEmail}</span>
              </a>
            </div>
            <a className="next-gen__kakao-btn" href={kakaoLink} target="_blank" rel="noopener noreferrer">
              <img src="/images/sub/02-next-generation/icon-kakao.svg" alt="" />
              <span>{kakaoLabel}</span>
            </a>
          </div>
          <div className="next-gen__pastor-watermark" aria-hidden="true">
            <img src="/images/sub/02-next-generation/icon-watermark.png" alt="" />
          </div>
        </div>

        {noticeTitle && (
          <div className="next-gen__notice">
            <div className="next-gen__notice-text">
              <p className="next-gen__notice-title">{noticeTitle}</p>
              <p className="next-gen__notice-desc">{noticeDescription}</p>
            </div>
            <a
              className="btn-basic-big btn-basic-big--trans next-gen__notice-btn"
              href={noticeButtonHref}
              target="_blank"
              rel="noopener noreferrer"
            >
              <i aria-hidden="true" />
              <span>{noticeButtonLabel}</span>
            </a>
          </div>
        )}
      </div>
    </section>
  );
}
