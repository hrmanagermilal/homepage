const FOOTER_NAV = [
  {
    title: "Introduction",
    links: [
      { label: "교회비전", path: "/introduction#introduction01" },
      { label: "담임목사", path: "/introduction#introduction02" },
      { label: "섬기는 분들", path: "/introduction#introduction03" },
      { label: "함께하는 교회", path: "/introduction#introduction04" },
    ],
  },
  {
    title: "다음세대",
    links: [
      { label: "청년부", path: "/nextgen#young-adults" },
      { label: "KM 청소년부", path: "/nextgen#km-youth" },
      { label: "EM 청소년부", path: "/nextgen#em-youth" },
      { label: "아동부", path: "/nextgen#children" },
      { label: "유치부", path: "/nextgen#kindergarten" },
      { label: "영유아부", path: "/nextgen#infants" },
    ],
  },
  {
    title: "사역",
    links: [
      { label: "양육", path: "/ministry#ministry01" },
      { label: "소그룹", path: "/ministry#ministry02" },
      { label: "가정", path: "/ministry#ministry03" },
      { label: "선교", path: "/ministry#ministry04" },
      { label: "장학", path: "/ministry#ministry05" },
      { label: "가스펠프로젝트", path: "/ministry#ministry08" },
      { label: "다니엘한글문화학교", path: "/ministry#ministry06" },
      { label: "러브토론토", path: "/ministry#ministry07" },
    ],
  },
  {
    title: "소식",
    links: [
      { label: "온라인 주보", path: "/news#bulletin" },
      { label: "공지", path: "/news#notice" },
      { label: "부고", path: "/news#obituary" },
    ],
  },
  {
    title: "온라인 헌금",
    links: [{ label: "온라인 헌금", path: "/online-giving" }],
  },
  {
    title: "밀알도서관",
    links: [
      { label: "밀알도서관", path: "https://milalbookcafe.com/", external: true }
    ],
  },
];

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer id="footer">
      <div className="footer__texture" aria-hidden="true">
        <img src="/images/common/footer-texture.jpg" alt="" />
      </div>

      <div className="wrap">
        <div className="footer__top">
          <div className="footer__brand" data-ani="top">
            <img className="footer__logo" src="/images/common/footer-logo.png" alt="밀알교회" />
          </div>

          <nav className="footer__nav" data-ani="top" aria-label="사이트 메뉴">
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

          <button className="btn-top" type="button" aria-label="맨 위로 이동" onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}>
            <img src="/images/common/icon-scroll-top.svg" alt="" />
          </button>
        </div>

        <div className="footer__divider" role="separator"></div>

        <div className="footer__bottom">
          <address className="footer__info" data-ani="top">
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
            <dl className="footer__info-item">
              <dt className="footer__info-label">COPYRIGHT</dt>
              <dd className="footer__info-value">ⓒ Milal Church. All Right Reserved. {currentYear}</dd>
            </dl>
          </address>


        </div>
      </div>
    </footer>
  );
}
