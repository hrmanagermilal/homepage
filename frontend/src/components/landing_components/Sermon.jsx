import { useMemo, useState } from "react";
import "./css/Sermon.css";

const DEFAULT_THUMBS = [
  "/images/main/youtube-thumb-01.jpg",
  "/images/main/youtube-thumb-03.jpg",
  "/images/main/youtube-thumb-02.jpg",
];

function getYoutubeThumb(url, index) {
  if (!url) return DEFAULT_THUMBS[index % DEFAULT_THUMBS.length];
  const match = url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{6,})/);
  if (!match?.[1]) return DEFAULT_THUMBS[index % DEFAULT_THUMBS.length];
  return `https://img.youtube.com/vi/${match[1]}/hqdefault.jpg`;
}

const TAB_LABELS = ["최신 설교", "주일예배 시리즈", "토요새벽기도회 강해"];

export default function Sermon({ items = [], section = null }) {
  const [activeTab, setActiveTab] = useState(0);
  const [isExpanded, setIsExpanded] = useState(false);

  const latestCards = useMemo(() => {
    if (!items.length) {
      return [
        { id: "fallback-1", title: "[가스펠 프로젝트 신약 4-8]", preacher: '"환난(患難)" (26.04.12)', youtube_url: "" },
        { id: "fallback-2", title: "[밀알교회 청년부 주일예배]", preacher: '"SOJOURNER"', youtube_url: "" },
        { id: "fallback-3", title: "[밀알교회 말씀묵상]", preacher: "2026.04.23 - 시편 104:1-9 | 신효성 목사", youtube_url: "" },
        { id: "fallback-4", title: "[가스펠 프로젝트 신약 4-8]", preacher: '"환난(患難)" (26.04.12)', youtube_url: "" },
        { id: "fallback-5", title: "[밀알교회 청년부 주일예배]", preacher: '"SOJOURNER"', youtube_url: "" },
        { id: "fallback-6", title: "[밀알교회 말씀묵상]", preacher: "2026.04.23 - 시편 104:1-9 | 신효성 목사", youtube_url: "" },
      ];
    }
    return items.slice(0, 6);
  }, [items]);

  return (
    <section className="main-youtube" id="sermon">
      <div className="wrap">
        <div className="main-youtube__head">
          <div className="main-title">
            <h2 data-heading="5xl" className="main-title__heading">{TAB_LABELS[activeTab]}</h2>
            <p className="main-title__sub">
              밀알교회는 찬양과 설교, 기도와 결단으로 이어지는 역동적인 예배공동체를 추구합니다.<br />
              현장예배의 유튜브 영상을 확인하세요.
            </p>
          </div>

          <nav className="main-youtube__tab" aria-label="영상 분류">
            <button className={`main-youtube__tab-btn${activeTab === 0 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 0} onClick={() => { setActiveTab(0); setIsExpanded(false); }}>최신 설교</button>
            <button className={`main-youtube__tab-btn${activeTab === 1 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 1} onClick={() => { setActiveTab(1); setIsExpanded(false); }}>주일예배 시리즈</button>
            <div className="main-youtube__tab-divider" aria-hidden="true"></div>
            <button className={`main-youtube__tab-btn${activeTab === 2 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 2} onClick={() => { setActiveTab(2); setIsExpanded(false); }}>토요새벽기도회 강해</button>
          </nav>
        </div>

        <div className={`main-youtube__panel${activeTab === 0 ? " is-active" : ""}`}>
          <div className="main-youtube__scroll">
            <div className={`main-youtube__list${isExpanded ? " is-expanded" : ""}`}>
              {latestCards.map((item, idx) => (
                <a
                  key={item.id || idx}
                  className={`youtube-card${idx === 0 ? " youtube-card--live" : ""}${idx > 2 ? " youtube-card--extra" : ""}`}
                  href={item.youtube_url || "#"}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <div className="youtube-card__thumb">
                    <img src={getYoutubeThumb(item.youtube_url, idx)} alt={item.title || "설교 썸네일"} />
                  </div>
                  <div className="youtube-card__gradient"></div>
                  {idx === 0 ? (
                    <div className="youtube-card__live-badge" aria-label="라이브 방송 중">
                      <span className="youtube-card__live-dot" aria-hidden="true"></span>
                      <span className="youtube-card__live-text">LIVE</span>
                    </div>
                  ) : null}
                  <div className="youtube-card__label">
                    <h3>{item.title || "제목 없음"}{item.preacher ? <><br />{item.preacher}</> : null}</h3>
                  </div>
                </a>
              ))}
            </div>
          </div>

          <div className={`main-youtube__more${isExpanded ? " is-expanded" : ""}`}>
            <button className={`main-youtube__more-btn`} type="button" onClick={() => setIsExpanded(true)}>
              <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M1 1L5 5.5L9 1" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              리스트 더보기
            </button>
            <a className="main-youtube__more-link" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer">
              유튜브 바로가기
            </a>
          </div>
        </div>

        <div className={`main-youtube__panel${activeTab === 1 ? " is-active" : ""}`}>
          <div className="series-list">
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="신앙생활의 기본원리 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="기도동행 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="교회같은 가정 가정같은 교회 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="기본으로 돌아가는 주님의 질문 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="밀알行전 시리즈 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="말씀동행 시리즈 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="감사동행 썸네일" /></div></a>
          </div>
        </div>

        <div className={`main-youtube__panel${activeTab === 2 ? " is-active" : ""}`}>
          <div className="series-list">
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 1 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 2 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 3 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 4 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 5 썸네일" /></div></a>
            <a className="series-card" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer"><div className="series-card__thumb"><img src="/images/main/test.webp" alt="토요새벽기도회 강해 6 썸네일" /></div></a>
          </div>
        </div>
      </div>
    </section>
  );
}
