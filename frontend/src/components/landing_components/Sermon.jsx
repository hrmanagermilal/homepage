import { useMemo, useState } from "react";
import { VideoCard } from "./Video";
import "./css/Sermon.css";

const TAB_LABELS = ["최신 설교", "시리즈 설교", "강해 설교"];

export default function Sermon({ items = [], section = null }) {
  const [activeTab, setActiveTab] = useState(0);
  const [isExpanded, setIsExpanded] = useState(false);

  console.log("Sermon items", items);

  const latestCards = useMemo(() => {
    if (!items.length) {
      return [
        { id: "fallback-1", title: "[가스펠 프로젝트 신약 4-8]", preacher: '"환난(患難)" (26.04.12)', youtube_url: "", live: true },
        { id: "fallback-2", title: "[밀알교회 청년부 주일예배]", preacher: '"SOJOURNER"', youtube_url: "", live: false },
        { id: "fallback-3", title: "[밀알교회 말씀묵상]", preacher: "2026.04.23 - 시편 104:1-9 | 신효성 목사", youtube_url: "", live: false },
        { id: "fallback-4", title: "[가스펠 프로젝트 신약 4-8]", preacher: '"환난(患難)" (26.04.12)', youtube_url: "", live: false },
        { id: "fallback-5", title: "[밀알교회 청년부 주일예배]", preacher: '"SOJOURNER"', youtube_url: "", live: false },
        { id: "fallback-6", title: "[밀알교회 말씀묵상]", preacher: "2026.04.23 - 시편 104:1-9 | 신효성 목사", youtube_url: "", live: false },
      ];
    }
    return items.slice(0, 6);
  }, [items]);

  const seriesCards = useMemo(() => items.filter(item => item.category_id === 2), [items]);
  const expositoryCards = useMemo(() => items.filter(item => item.category_id === 3), [items]);

  return (
    <section className="main-youtube" id="sermon">
      <div className="wrap">
        <div className="main-youtube__head" data-ani="top">
          <div className="main-title">
            <h2 data-heading="5xl" className="main-title__heading">{TAB_LABELS[activeTab]}</h2>
            <p className="main-title__sub">
              밀알교회는 찬양과 설교, 기도와 결단으로 이어지는 역동적인 예배공동체를 추구합니다.<br />
              현장예배의 유튜브 영상을 확인하세요.
            </p>
          </div>

          <nav className="main-youtube__tab" aria-label="영상 분류">
            <button className={`main-youtube__tab-btn${activeTab === 0 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 0} onClick={() => { setActiveTab(0); setIsExpanded(false); }}>최신 설교</button>
            <button className={`main-youtube__tab-btn${activeTab === 1 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 1} onClick={() => { setActiveTab(1); setIsExpanded(false); }}>시리즈 설교</button>
            <div className="main-youtube__tab-divider" aria-hidden="true"></div>
            <button className={`main-youtube__tab-btn${activeTab === 2 ? " is-active" : ""}`} type="button" aria-selected={activeTab === 2} onClick={() => { setActiveTab(2); setIsExpanded(false); }}>강해 설교</button>
          </nav>
        </div>

        <div className={`main-youtube__panel${activeTab === 0 ? " is-active" : ""}`}>
          <div className="main-youtube__scroll">
            <div className={`main-youtube__list${isExpanded ? " is-expanded" : ""}`}>
              {latestCards.map((item, idx) => (
                <VideoCard
                  key={item.id || idx}
                  url={item.youtube_url}
                  title={item.title}
                  preacher={item.preacher}
                  live={item.live}
                  index={idx}
                  className={idx > 2 ? " youtube-card--extra" : ""}
                  thumbnail={item.thumbnail}
                  hide_title={false}
                />
              ))}
            </div>
          </div>

          <div className={`main-youtube__more${isExpanded ? " is-expanded" : ""}`}>
            <button className={`main-youtube__more-btn`} type="button" onClick={() => setIsExpanded(true)}>
              <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M1 1L5 5.5L9 1" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              리스트 더 보기
            </button>
            <a className="main-youtube__more-link" href="https://youtube.com/@milalchurch" target="_blank" rel="noopener noreferrer">
              유튜브 바로가기
            </a>
          </div>
        </div>

        <div className={`main-youtube__panel${activeTab === 1 ? " is-active" : ""}`}>
          <div className="main-youtube__scroll">
            <div className={`main-youtube__list${isExpanded ? " is-expanded" : ""}`}>
              {seriesCards.map((item, idx) => (
                <VideoCard
                  key={item.id || idx}
                  url={item.youtube_url}
                  title={item.title}
                  preacher={item.preacher}
                  live={item.live}
                  index={idx}
                  className={idx > 2 ? " youtube-card--extra" : ""}
                  thumbnail={item.thumbnail}
                  hide_title={false}
                />
              ))}
            </div>
          </div>

          <div className={`main-youtube__more${isExpanded ? " is-expanded" : ""}`}>
            <button className="main-youtube__more-btn" type="button" onClick={() => setIsExpanded(true)}>
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

        <div className={`main-youtube__panel${activeTab === 2 ? " is-active" : ""}`}>
          <div className="main-youtube__scroll">
            <div className={`main-youtube__list${isExpanded ? " is-expanded" : ""}`}>
              {expositoryCards.map((item, idx) => (
                <VideoCard
                  key={item.id || idx}
                  url={item.youtube_url}
                  title={item.title}
                  preacher={item.preacher}
                  live={item.live}
                  index={idx}
                  className={idx > 2 ? " youtube-card--extra" : ""}
                  thumbnail={item.thumbnail}
                  hide_title={false}
                />
              ))}
            </div>
          </div>

          <div className={`main-youtube__more${isExpanded ? " is-expanded" : ""}`}>
            <button className="main-youtube__more-btn" type="button" onClick={() => setIsExpanded(true)}>
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
      </div>
    </section>
  );
}
