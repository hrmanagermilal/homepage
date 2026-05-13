import { useState, useRef, useEffect, useCallback } from "react";

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
    num: "02", label: "다음세대", path: "/nextgen/young-adults",
    subs: [
      { label: "청년부", path: "/nextgen/young-adults" },
      { label: "KM 청소년부", path: "/nextgen/km-youth" },
      { label: "EM 청소년부", path: "/nextgen/em-youth" },
      { label: "아동부", path: "/nextgen/children" },
      { label: "유치부", path: "/nextgen/kindergarten" },
      { label: "유아부", path: "/nextgen/preschool" },
      { label: "영아부", path: "/nextgen/infants" },
    ],
  },
  {
    num: "03", label: "사역", path: "/ministry",
    subs: [
      { label: "양육", path: "/ministry" },
      { label: "소그룹", path: "/ministry" },
      { label: "가정", path: "/ministry" },
      { label: "선교", path: "/ministry" },
      { label: "장학", path: "/ministry" },
      { label: "가스펠프로젝트", path: "/ministry" },
      { label: "다니엘한글문화학교", path: "#" },
      { label: "러브토론토", path: "https://lovetoronto.org/", external: true },
    ],
  },
  {
    num: "04", label: "소식", path: "/news/notice",
    subs: [
      { label: "공지", path: "/news/notice" },
      { label: "부고", path: "/news/obituary" },
    ],
  },
  {
    num: "05", label: "온라인 헌금", path: "/online-giving",
    subs: [],
  },
];

export default function Header({ quickLinks = [], landingTitles = [] }) {
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
      audio.play().then(() => setBgmPlaying(true)).catch(() => {});
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
      audio.play().then(() => setBgmPlaying(true)).catch(() => {});
    } else {
      audio.pause();
      setBgmPlaying(false);
    }
  };

  const handleFullMenuItemEnter = useCallback((idx) => {
    setFullMenuHoveredIdx(idx);
    if (!gnbRef.current || !itemRefs.current[idx]) return;
    const gnbRect = gnbRef.current.getBoundingClientRect();
    const itemRect = itemRefs.current[idx].getBoundingClientRect();
    const relativeTop = itemRect.top - gnbRect.top + itemRect.height / 2;
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
    <>
      <header className="site-header" role="banner">
        <h1 id="site-title" className="sound-only">밀알교회</h1>
        <div className="site-header__inner">

          <a className="site-header__logo" href="/">
            <img src="/images/common/logo.png" alt="밀알교회" />
          </a>

          <button
            className="site-header__volume"
            type="button"
            aria-label={bgmPlaying ? "배경음악 일시 정지" : "배경음악 재생"}
            aria-pressed={bgmPlaying}
            onClick={toggleBgm}
          >
            <img
              src={bgmPlaying ? "/images/common/icon-volume.svg" : "/images/common/icon-volume--mute.svg"}
              alt=""
            />
          </button>

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
            <a className="site-header__news-btn" href="/news/notice">
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
                        <a className="full-menu__gnb-title" href={item.path}>
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
                      style={{ top: fullMenuHoveredIdx === idx ? subTopPx : undefined }}
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

                <p className="full-menu__copy">ⓒ MILAL CHURCH .All Right Reserved.</p>
              </nav>
            </div>

            <div className="full-menu__info">
              <div className="full-menu__info-section">
                <h2 className="full-menu__info-title">공지사항</h2>
                <a className="full-menu__info-card" href="/news/notice">
                  <span className="full-menu__info-card-text">밀알교회 홈페이지가 새롭게 리뉴얼 되었습니다.</span>
                  <span className="full-menu__info-arrow">
                    <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                      <path d="M1 5H13M13 5L9 1M13 5L9 9" stroke="white" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  </span>
                </a>
              </div>

              <div className="full-menu__info-section">
                <h2 className="full-menu__info-title">최근 부고 소식</h2>
                <a className="full-menu__info-card" href="/news/obituary">
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
                      <span className="full-menu__shortcut-label">실시간 예배 보러가기</span>
                    </a>
                  </li>
                  <li>
                    <a className="full-menu__shortcut" href="#" target="_blank" rel="noopener noreferrer">
                      <span className="full-menu__shortcut-icon">
                        <img src="/images/common/ic-fullmenu02.svg" alt="" aria-hidden="true" />
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
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
