import { useState, useRef, useEffect, useCallback } from "react";
import ThemeSwitcher from "./ThemeSwitcher";
import InterpreterModeIcon from '@mui/icons-material/InterpreterMode';

const NAV_ITEMS = [
  {
    num: "01", label: "Introduction", path: "/introduction",
    subs: [
      { label: "교회비전", path: "/introduction#introduction01" },
      { label: "담임목사", path: "/introduction#introduction02" },
      { label: "섬기는 분들", path: "/introduction#introduction03" },
      { label: "함께하는 교회", path: "/introduction#introduction04" },
    ],
  },
  {
    num: "02", label: "다음세대", path: "/nextgen#young-adults",
    subs: [
      { label: "청년부", path: "/nextgen#young-adults" },
      { label: "헤세드 청소년부(KM)", path: "/nextgen#km-youth" },
      { label: "오하나 청소년부(EM)", path: "/nextgen#em-youth" },
      { label: "카리스 아동부", path: "/nextgen#children" },
      { label: "조이 유치부", path: "/nextgen#kindergarten" },
      { label: "미라클 영유아부", path: "/nextgen#infants" },
    ],
  },
  {
    num: "03", label: "사역", path: "/ministry",
    subs: [
      { label: "소그룹", path: "/ministry#ministry02" },
      { label: "양육", path: "/ministry#ministry01" },
      { label: "가정", path: "/ministry#ministry03" },
      { label: "선교", path: "/ministry#ministry04" },
      { label: "장학", path: "/ministry#ministry05" },
      { label: "가스펠프로젝트", path: "/ministry#ministry08" },
      { label: "다니엘한글문화학교", path: "/ministry#ministry06" },
      { label: "러브토론토", path: "/ministry#ministry07" },
    ],
  },
  {
    num: "04", label: "소식", path: "/news#notice",
    subs: [
      { label: "온라인 주보", path: "/news#bulletin" },
      { label: "공지", path: "/news#notice" },
      { label: "부고", path: "/news#obituary" },
    ],
  },
  {
    num: "05", label: "온라인 헌금", path: "/online-giving",
    subs: [],
  },
];

export default function Header({ quickLinks = [], landingTitles = [], theme, setTheme, notice = [] }) {
  const [fullMenuOpen, setFullMenuOpen] = useState(false);
  const [bgmPlaying, setBgmPlaying] = useState(false);
  const [fullMenuHoveredIdx, setFullMenuHoveredIdx] = useState(-1);
  const [subTopPx, setSubTopPx] = useState(0);

  const audioRef = useRef(null);
  const gnbRef = useRef(null);
  const itemRefs = useRef([]);

  useEffect(() => {
    const audio = new Audio("/milal-bgm.wav");
    //audio.loop = false;
    //audio.preload = "auto";
    audioRef.current = audio;
    audio.pause();

    const playOnGesture = (e) => {
      if (e.target?.closest?.(".site-header__volume")) return;
      if (!audio.paused) return;
      audio.play().then(() => setBgmPlaying(true)).catch(() => { });
      document.removeEventListener("pointerdown", playOnGesture);
      document.removeEventListener("keydown", playOnGesture);
    };

    audio.play().then(() => setBgmPlaying(true)).catch(() => {
      document.addEventListener("pointerdown", playOnGesture);
      document.addEventListener("keydown", playOnGesture);
    });

    return () => {
      audio.pause();
      document.removeEventListener("pointerdown", playOnGesture);
      document.removeEventListener("keydown", playOnGesture);
    };
  }, []);

  useEffect(() => {
    document.body.style.overflow = fullMenuOpen ? "hidden" : "";
    return () => { document.body.style.overflow = ""; };
  }, [fullMenuOpen]);

  const toggleBgm = () => {
    const audio = audioRef.current;
    if (!audio) return;
    if (audio.paused) {
      audio.play().then(() => setBgmPlaying(true)).catch(() => { });
    } else {
      audio.pause();
      setBgmPlaying(false);
    }
  };

  const handleInternalLinkClick = useCallback((e) => {
    const link = e.target?.closest?.("a[href]");
    if (!link) return;

    const fullMenuIdxAttr = link.getAttribute("data-fullmenu-index");
    const hasFullMenuSubs = link.getAttribute("data-has-subs") === "true";
    if (fullMenuIdxAttr && hasFullMenuSubs) {
      const isMobileMenu = typeof window !== "undefined" && window.matchMedia("(max-width: 540px)").matches;
      if (isMobileMenu) {
        const idx = Number(fullMenuIdxAttr);
        e.preventDefault();
        setFullMenuHoveredIdx((prev) => (prev === idx ? -1 : idx));
        return;
      }
    }

    const href = link.getAttribute("href") || "";
    if (!href.startsWith("/")) return;
    if (link.target && link.target !== "_self") return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;

    const nextUrl = new URL(href, window.location.origin);
    if (nextUrl.origin !== window.location.origin) return;

    e.preventDefault();

    const nextPath = `${nextUrl.pathname}${nextUrl.search}${nextUrl.hash}`;
    const currentPath = `${window.location.pathname}${window.location.search}${window.location.hash}`;
    if (nextPath === currentPath) {
      if (nextUrl.hash) {
        window.dispatchEvent(new HashChangeEvent("hashchange"));
      }
      setFullMenuOpen(false);
      return;
    }

    window.history.pushState({}, "", nextPath);
    window.dispatchEvent(new Event("locationchange"));
    if (nextUrl.hash) {
      window.dispatchEvent(new HashChangeEvent("hashchange"));
    }
    setFullMenuOpen(false);
    window.scrollTo({ top: 0, behavior: "auto" });
  }, []);

  const handleFullMenuItemEnter = useCallback((idx) => {
    const isDesktop = typeof window !== "undefined" && window.matchMedia("(min-width: 1024px)").matches;
    if (!isDesktop) return;

    setFullMenuHoveredIdx(idx);
    if (!gnbRef.current || !itemRefs.current[idx]) return;
    const item = itemRefs.current[idx];
    const relativeTop = item.offsetTop + Math.round(item.offsetHeight / 2);
    setSubTopPx(relativeTop);
  }, []);

  useEffect(() => {
    if (!fullMenuOpen) {
      setFullMenuHoveredIdx(-1);
      return;
    }

    const isDesktop = typeof window !== "undefined" && window.matchMedia("(min-width: 1024px)").matches;
    if (!isDesktop) return;

    const timer = window.setTimeout(() => {
      handleFullMenuItemEnter(0);
    }, 150);

    return () => window.clearTimeout(timer);
  }, [fullMenuOpen, handleFullMenuItemEnter]);

  return (
    <div onClickCapture={handleInternalLinkClick}>
      <header className="site-header" role="banner">
        <h1 id="site-title" className="sound-only">밀알교회</h1>
        <div className="site-header__inner">

          <a className="site-header__logo" href="/">
            <img src="/images/common/logo.png" alt="밀알교회" />
          </a>

          <nav className="site-header__gnb" aria-label="주 메뉴">
            <ul className="site-header__gnb-list">
              {NAV_ITEMS.map((item, idx) => (
                <li key={idx} className="site-header__gnb-item-wrap">
                  <a className="site-header__gnb-item" href={item.path}>
                    {item.label}
                  </a>
                  {item.subs.length > 0 && (
                    <ul className="site-header__gnb-sub">
                      {item.subs.map((sub, si) => (
                        <li key={si}>
                          <a
                            className="site-header__gnb-sub-item"
                            href={sub.path}
                            {...(sub.external ? { target: "_blank", rel: "noopener noreferrer" } : {})}
                          >
                            {sub.label}
                          </a>
                        </li>
                      ))}
                    </ul>
                  )}
                </li>
              ))}
            </ul>
          </nav>

          <div className="site-header__util">
            {false && <ThemeSwitcher theme={theme} setTheme={setTheme} /> /*ThemeSwitcher 비활성화*/}
            <a className="site-header__news-btn" href="/news#notice">
              <img src="/images/common/icon-header-news.svg" alt="" />
              <span>밀알 소식 바로가기</span>
            </a>
            <button
              className="site-header__hamburger"
              type="button"
              aria-label="전체 메뉴 열기"
              onClick={() => setFullMenuOpen(true)}
            >
              <span className="site-header__hamburger-line"></span>
              <span className="site-header__hamburger-line"></span>
            </button>
          </div>

        </div>
      </header>

      <div
        className={`full-menu${fullMenuOpen ? " is-open" : ""}`}
        id="fullMenu"
        role="dialog"
        aria-modal="true"
        aria-label="전체 메뉴"
      >
        <div className="full-menu__bg" aria-hidden="true"></div>
        <div className="full-menu__texture" aria-hidden="true"></div>
        <div className="full-menu__right-bg" aria-hidden="true"></div>

        <div className="full-menu__header">
          <div className="wrap full-menu__header-inner">
            <a className="full-menu__logo" href="/">
              <img src="/images/common/logo.png" alt="밀알교회" />
            </a>
            <button
              className="full-menu__close-btn"
              type="button"
              aria-label="메뉴 닫기"
              onClick={() => setFullMenuOpen(false)}
            >
              <i></i>
              CLOSE
            </button>
          </div>
        </div>

        <div className="full-menu__body">
          <div className="wrap full-menu__body-inner">
            <div className="full-menu__list">
              <nav className="full-menu__gnb" ref={gnbRef} aria-label="전체 메뉴 내비게이션">
                <ul className="full-menu__gnb-list">
                  {NAV_ITEMS.map((item, idx) => (
                    <li
                      key={idx}
                      ref={(el) => (itemRefs.current[idx] = el)}
                      className={`full-menu__gnb-item${fullMenuHoveredIdx === idx ? " is-active" : ""}`}
                      onMouseEnter={() => handleFullMenuItemEnter(idx)}
                    >
                      <div className="full-menu__gnb-label">
                        <span className="full-menu__gnb-num">{item.num}</span>
                        <a
                          className="full-menu__gnb-title"
                          href={item.path}
                          data-fullmenu-index={idx}
                          data-has-subs={item.subs.length > 0}
                        >
                          {item.label}
                        </a>
                      </div>
                    </li>
                  ))}
                </ul>

                {NAV_ITEMS.map((item, idx) =>
                  item.subs.length > 0 ? (
                    <ul
                      key={idx}
                      className={`full-menu__gnb-sub${fullMenuHoveredIdx === idx ? " is-active" : ""}`}
                      style={subTopPx > 0 ? { top: subTopPx } : undefined}
                    >
                      {item.subs.map((sub, si) => (
                        <li key={si}>
                          <a
                            className="full-menu__gnb-sub-link"
                            href={sub.path}
                            {...(sub.external ? { target: "_blank", rel: "noopener noreferrer" } : {})}
                          >
                            {sub.label}
                          </a>
                        </li>
                      ))}
                    </ul>
                  ) : null
                )}

                <p className="full-menu__copy">ⓒ Milal Church. All Right Reserved.</p>
              </nav>
            </div>

            <div className="full-menu__info">
              <div className="full-menu__info-section">
                <h2 className="full-menu__info-title">공지사항</h2>
                {(() => {
                  const n = notice.find((item) => !item.image);
                  return (
                    <a className="full-menu__info-card" href={n ? `/news/notice/${n.id}` : "/news#notice"}>
                      <span className="full-menu__info-card-text">{n ? n.title : "공지사항을 확인해 주세요."}</span>
                      <span className="full-menu__info-arrow">
                        <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                          <path d="M1 5H13M13 5L9 1M13 5L9 9" stroke="white" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </span>
                    </a>
                  );
                })()}
              </div>

              <div className="full-menu__info-section">
                <h2 className="full-menu__info-title">최근 부고 소식</h2>
                <a className="full-menu__info-card" href="/news#obituary">
                  <span className="full-menu__obituary-icon">
                    <img src="/images/common/icon-obituary-cross.svg" alt="" />
                  </span>
                  <span className="full-menu__info-card-text">부고 소식은 교회 사무실을 통해 알려주세요.</span>
                  <span className="full-menu__info-arrow">
                    <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                      <path d="M1 5H13M13 5L9 1M13 5L9 9" stroke="white" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  </span>
                </a>
              </div>

              <div className="full-menu__info-section">
                <h2 className="full-menu__info-title">바로가기</h2>
                <ul className="full-menu__shortcuts">
                  <li>
                    <a className="full-menu__shortcut" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <img src="/images/common/ic-fullmenu01.svg" alt="" aria-hidden="true" />
                      </span>
                      <span className="full-menu__shortcut-label">예배 영상 보러가기</span>
                    </a>
                  </li>
                  <li>
                    <a className="full-menu__shortcut" href="https://captionkit.io/c/milal-etvynx/l/en-US" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <InterpreterModeIcon sx={{ display: 'block', fontSize: 'inherit' }} />
                      </span>
                      <span className="full-menu__shortcut-label">실시간예배 통역 바로가기</span>
                    </a>
                  </li>
                  <li>
                    <a className="full-menu__shortcut" href="/ministry#ministry06" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <img src="/images/common/ic-quick03.png" alt="" aria-hidden="true" />
                      </span>
                      <span className="full-menu__shortcut-label">다니엘한글문화학교 바로가기</span>
                    </a>
                  </li>
                  <li>
                    <a className="full-menu__shortcut" href="https://lovetoronto.org/" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <img src="/images/common/ic-fullmenu03.png" alt="" aria-hidden="true" />
                      </span>
                      <span className="full-menu__shortcut-label">러브 토론토 바로가기</span>
                    </a>
                  </li>
                  <li>
                    <a className="full-menu__shortcut" href="https://milalbookcafe.com/" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path d="M10.2697 2.2016C8.87249 1.4662 6.11888 2.88718 5.32785 4.13815C4.97545 4.69794 5.00038 5.10135 5.00038 5.33022V17.5746L15.3266 24L17.2684 22.9397V11.0152L6.66613 4.92793C7.23507 4.21189 8.51463 3.33875 9.47407 3.68943L18.9176 8.74022L18.9177 22.0242L20.8644 20.962V7.67834L10.2697 2.2016Z" fill="currentColor" />
                        </svg>
                      </span>
                      <span className="full-menu__shortcut-label">밀알도서관 바로가기</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <button
          type="button"
          aria-label="관리자 페이지"
          style={{ position: 'absolute', bottom: '24px', right: '32px', zIndex: 10, background: 'none', border: 'none', cursor: 'pointer', color: 'rgba(255,255,255,0.35)', padding: '8px', lineHeight: 0 }}
          onClick={(e) => {
            e.stopPropagation();
            window.open(`http://${window.location.hostname}:81`, '_blank', 'noopener,noreferrer');
          }}
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
          </svg>
        </button>
      </div>
    </div>
  );
}
