const FOOTER_NAV = [
  {
    title: "Introduction",
    links: [
      { label: "교회비전", path: "/introduction#introduction01" },
      { label: "섬기는 분들", path: "/introduction#introduction02" },
      { label: "함께하는 교회", path: "/introduction#introduction03" },
    ],
  },
  {
    title: "다음세대",
    links: [
      { label: "청년부", path: "/nextgen/young-adults" },
      { label: "KM 청년부", path: "/nextgen/km-youth" },
      { label: "EM 청년부", path: "/nextgen/em-youth" },
      { label: "아동부", path: "/nextgen/children" },
    ],
  },
  {
    title: "사역",
    links: [
      { label: "양육", path: "/ministry" },
      { label: "선교", path: "/ministry" },
      { label: "다니엘한글문화학교", path: "#" },
      { label: "러브토론토", path: "https://lovetoronto.org/", external: true },
    ],
  },
  {
    title: "소식",
    links: [
      { label: "공지", path: "/news/notice" },
      { label: "부고", path: "/news/obituary" },
    ],
  },
  {
    title: "온라인 헌금",
    links: [{ label: "온라인 헌금", path: "/online-giving" }],
  },
];

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer id="footer">
      <div className="footer__texture" aria-hidden="true">
        <img src="/images/common/footer-texture.jpg" alt="" />
      </div>

      <button className="btn-top" type="button" aria-label="맨 위로 이동" onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}>
        <img src="/images/common/icon-scroll-top.svg" alt="" />
      </button>

      <div className="wrap">
        <div className="footer__top">
          <div className="footer__brand">
            <img className="footer__logo" src="/images/common/footer-logo.png" alt="밀알교회" />
            <p className="footer__copy">ⓒ MILAL CHURCH .All Right Reserved. {currentYear}</p>
          </div>

          <nav className="footer__nav" aria-label="사이트 메뉴">
            {FOOTER_NAV.map((group) => (
              <div key={group.title} className="footer-nav__col">
                <a className="footer-nav__title" href={group.links[0]?.path || "#"}>
                  {group.title}
                </a>
                <ul className="footer-nav__links">
                  {group.links.map((link) => (
                    <li key={link.label}>
                      <a
                        className="footer-nav__link"
                        href={link.path}
                        {...(link.external ? { target: "_blank", rel: "noopener noreferrer" } : {})}
                      >
                        {link.label}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </nav>
        </div>

        <div className="footer__divider" role="separator"></div>

        <div className="footer__bottom">
          <address className="footer__info">
            <dl className="footer__info-item">
              <dt className="footer__info-label">ADDRESS</dt>
              <dd className="footer__info-value">405 Gordon Baker Rd. Toronto Ontario Canada M2H 2S6</dd>
            </dl>
            <dl className="footer__info-item">
              <dt className="footer__info-label">TEL</dt>
              <dd className="footer__info-value"><a href="tel:+14162264190">416-226-4190</a></dd>
            </dl>
            <dl className="footer__info-item">
              <dt className="footer__info-label">FAX</dt>
              <dd className="footer__info-value">416-226-5308</dd>
            </dl>
            <dl className="footer__info-item">
              <dt className="footer__info-label">E-MAIL</dt>
              <dd className="footer__info-value"><a href="mailto:milalchurch405@gmail.com">milalchurch405@gmail.com</a></dd>
            </dl>
          </address>

          <ul className="footer__policy">
            <li><a className="footer-policy-btn" href="/privacy/01-privacy01.html">개인정보처리방침</a></li>
            <li><a className="footer-policy-btn" href="/privacy/01-privacy02.html">이메일무단수집거부</a></li>
          </ul>
        </div>
      </div>
    </footer>
  );
}
