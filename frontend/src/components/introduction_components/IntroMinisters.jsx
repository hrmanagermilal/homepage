import { useState, useEffect } from "react";
import "./css/IntroMinisters.css";

const TABS = [
  { label: "목회자", panel: "ministerPanel-0" },
  { label: "시무장로", panel: "ministerPanel-1" },
  { label: "직원", panel: "ministerPanel-2" },
];

const FALLBACK_MINISTERS = [
  { name: "박형일", role: "목사", img: "/images/sub/01-introduction/minister-01.jpg", hasDetail: true },
  { name: "이기쁨", role: "목사", img: "/images/sub/01-introduction/minister-02.jpg", hasDetail: true },
  { name: "김준영", role: "목사", img: "/images/sub/01-introduction/minister-03.jpg", hasDetail: true },
  { name: "신효성", role: "목사", img: "/images/sub/01-introduction/minister-04.jpg", hasDetail: true },
  { name: "차승현", role: "목사", img: "/images/sub/01-introduction/minister-05.jpg", hasDetail: true },
  { name: "이용", role: "목사", img: "/images/sub/01-introduction/minister-06.jpg", hasDetail: true },
  { name: "오성요", role: "목사", img: "/images/sub/01-introduction/minister-07.jpg", hasDetail: true },
  { name: "배상진", role: "목사", img: "/images/sub/01-introduction/minister-08.jpg", hasDetail: true },
  { name: "Jonathan Kim", role: "목사", img: "/images/sub/01-introduction/minister-09.jpg", hasDetail: true },
  { name: "최수라", role: "전도사", img: "/images/sub/01-introduction/minister-10.jpg", hasDetail: true },
  { name: "최정수", role: "전도사", img: "/images/sub/01-introduction/minister-11.jpg", hasDetail: true },
  { name: "김비치", role: "전도사", img: "/images/sub/01-introduction/minister-12.jpg", hasDetail: true },
  { name: "김진아", role: "전도사", img: "/images/sub/01-introduction/minister-13.jpg", hasDetail: true },
  { name: "주은지", role: "전도사", img: "/images/sub/01-introduction/minister-14.jpg", hasDetail: true },
];

const FALLBACK_ELDERS = [
  { name: "목상수", role: "장로", img: "/images/sub/01-introduction/elder-01.jpg" },
  { name: "김준덕", role: "장로", img: "/images/sub/01-introduction/elder-02.jpg" },
  { name: "이강식", role: "장로", img: "/images/sub/01-introduction/elder-03.jpg" },
  { name: "노명신", role: "장로", img: "/images/sub/01-introduction/elder-04.jpg" },
  { name: "정진관", role: "장로", img: "/images/sub/01-introduction/elder-05.jpg" },
  { name: "김형렬", role: "장로", img: "/images/sub/01-introduction/elder-06.jpg" },
  { name: "권규찬", role: "장로", img: "/images/sub/01-introduction/elder-07.jpg" },
  { name: "김태우", role: "장로", img: "/images/sub/01-introduction/elder-08.jpg" },
];

const FALLBACK_STAFF = [
  { name: "김선덕", role: "사무간사", img: "/images/sub/01-introduction/deacon-01.jpg" },
  { name: "조영범", role: "음향간사", img: "/images/sub/01-introduction/deacon-02.jpg" },
  { name: "서초희", role: "미디어간사", img: "/images/sub/01-introduction/deacon-03.jpg" },
];

const MINISTER_DETAILS = {
  "박형일":     { category: "목회자", email: "hyungilpark@milalchurch.com",  tags: [],                                                                                         position: ["담임목사 / Senior Pastor"] },
  "이기쁨":     { category: "목회자", email: "kippeumlee@milalchurch.com",   tags: ["목회행정(선임)", "목회부", "공동체(생명, 충성)", "공간기획"],                              position: ["목사"] },
  "김준영":     { category: "목회자", email: "junyoungkim@milalchurch.com",  tags: ["예배부(1부/2부 찬양인도)", "봉사부(건물관리/주차/경조)", "공동체(기쁨,진리)"],            position: ["목사"] },
  "신효성":     { category: "목회자", email: "rev.shin@milalchurch.com",     tags: ["청년부", "선교부", "장학"],                                                               position: ["목사"] },
  "차승현":     { category: "목회자", email: "seunghyuncha@milalchurch.com", tags: ["청소년부(KM 해세드)", "캠퍼스 신입생 심방", "청소년부 선교 및 통합훈련"],                  position: ["목사"] },
  "이용":       { category: "목회자", email: "unglee@milalchurch.com",       tags: ["교육총괄", "가스펠프로젝트", "목회기획", "공동체(은혜,영광)"],                            position: ["목사"] },
  "오성요":     { category: "목회자", email: "osungyo@milalchurch.com",      tags: ["목양(소그룹)", "찬양인도(주일3부, 금요찬양집회)", "친교부 공동체(믿음,온유)"],             position: ["목사"] },
  "배상진":     { category: "목회자", email: "sangjinbae@milalchurch.com",   tags: ["훈련사역부", "청장년부", "다니엘한글문화학교", "Child Care", "공동체(감사)"],             position: ["목사"] },
  "Jonathan Kim": { category: "목회자", email: "jonathankim@milalchurch.com", tags: ["청소년부(EM 오하나)"],                                                                  position: ["목사"] },
  "최수라":     { category: "목회자", email: "soorachoi@milalchurch.com",    tags: ["새가족", "가정사역부(마더/파더 와이즈)", "공동체(지혜 A,B)"],                             position: ["전도사"] },
  "최정수":     { category: "목회자", email: "jeongsuchoi@milalchurch.com",  tags: ["시니어 사역 (다윗/여호수아/모세회)"],                                                    position: ["전도사"] },
  "김비치":     { category: "목회자", email: "bichi.kim@milalchurch.com",    tags: ["유치부"],                                                                                position: ["전도사"] },
  "김진아":     { category: "목회자", email: "jina.kim@milalchurch.com",     tags: ["아동부"],                                                                                position: ["전도사"] },
  "주은지":     { category: "목회자", email: "eunji.ju@milalchurch.com",     tags: ["영유아부"],                                                                              position: ["전도사"] },
};

function buildPanels(members) {
  if (!members.length) {
    return [FALLBACK_MINISTERS, FALLBACK_ELDERS, FALLBACK_STAFF];
  }
  const ministers = members.filter((m) => m.category === "목회자").map((m) => ({
    name: m.name, role: m.title || m.role || "목사",
    img: m.picture ? (m.picture.startsWith("http") ? m.picture : `/uploads/members/${m.picture}`) : "",
    hasDetail: true,
  }));
  const elders = members.filter((m) => m.category === "장로").map((m) => ({
    name: m.name, role: m.title || "장로",
    img: m.picture ? (m.picture.startsWith("http") ? m.picture : `/uploads/members/${m.picture}`) : "",
  }));
  const staff = members.filter((m) => m.category === "간사").map((m) => ({
    name: m.name, role: m.title || "간사",
    img: m.picture ? (m.picture.startsWith("http") ? m.picture : `/uploads/members/${m.picture}`) : "",
  }));
  return [ministers.length ? ministers : FALLBACK_MINISTERS, elders.length ? elders : FALLBACK_ELDERS, staff.length ? staff : FALLBACK_STAFF];
}

function MinisterCard({ person, onOpen }) {
  return (
    <li className="minister-card" onClick={person.hasDetail ? () => onOpen(person) : undefined}
        role={person.hasDetail ? "button" : undefined} tabIndex={person.hasDetail ? 0 : undefined}
        onKeyDown={person.hasDetail ? (e) => { if (e.key === "Enter" || e.key === " ") { e.preventDefault(); onOpen(person); } } : undefined}>
      <div className="minister-card__thumb">
        <img src={person.img} alt={`${person.role} ${person.name}`} />
        {person.hasDetail && (
          <div className="minister-card__hover">
            <div className="minister-card__hover-btn">
              <i /><span>자세히 보기</span>
            </div>
          </div>
        )}
      </div>
      <div className="minister-card__body">
        <p className="minister-card__role">{person.role}</p>
        <p className="minister-card__name">{person.name}</p>
      </div>
    </li>
  );
}

function MinisterModal({ person, onClose }) {
  const detail = MINISTER_DETAILS[person.name] || {};
  const tags = detail.tags || [];
  const positions = detail.position || [person.role];

  useEffect(() => {
    document.body.style.overflow = "hidden";
    const onKey = (e) => { if (e.key === "Escape") onClose(); };
    document.addEventListener("keydown", onKey);
    return () => { document.body.style.overflow = ""; document.removeEventListener("keydown", onKey); };
  }, [onClose]);

  return (
    <div className="minister-modal is-open" role="dialog" aria-modal="true" aria-labelledby="ministerModalName">
      <div className="minister-modal__dim" onClick={onClose} />
      <div className="minister-modal__box">
        <button className="minister-modal__close" type="button" aria-label="팝업 닫기" onClick={onClose} />
        <div className="minister-modal__photo">
          <img src={person.img} alt={`${person.role} ${person.name}`} />
        </div>
        <div className="minister-modal__cont">
          <div className="minister-modal__head">
            <div>
              <p className="minister-modal__category">{detail.category || "목회자"}</p>
              <h4 className="minister-modal__name" id="ministerModalName">{person.name} {person.role}</h4>
            </div>
            {tags.length > 0 && (
              <div className="minister-modal__tags">
                {tags.map((tag, i) => <span key={i} className="minister-modal__tag">{tag}</span>)}
              </div>
            )}
            {detail.email && (
              <a className="minister-modal__email" href={`mailto:${detail.email}`}>{detail.email}</a>
            )}
          </div>
          <div className="minister-modal__divider" aria-hidden="true" />
          <ul className="minister-modal__position" data-list="dot">
            {positions.map((pos, i) => <li key={i}>{pos}</li>)}
          </ul>
        </div>
      </div>
    </div>
  );
}

export default function IntroMinisters({ members = [] }) {
  const [activeTab, setActiveTab] = useState(0);
  const [selectedPerson, setSelectedPerson] = useState(null);
  const panels = buildPanels(members);

  return (
    <>
      <section id="introduction03" className="intro-ministers">
        <div className="wrap">
          <div className="intro-ministers__head">
            <h3 data-heading="5xl">섬기는 분들</h3>
            <div className="tab-menu intro-ministers__tab" role="tablist" aria-label="섬기는 분들 탭">
              {TABS.map((tab, idx) => (
                <button
                  key={tab.label}
                  className={`tab-btn${idx === TABS.length - 1 ? " tab-btn--sep" : ""}${activeTab === idx ? " is-active" : ""}`}
                  type="button"
                  role="tab"
                  aria-selected={activeTab === idx}
                  aria-controls={tab.panel}
                  onClick={() => setActiveTab(idx)}
                >
                  {tab.label}
                </button>
              ))}
            </div>
          </div>

          {panels.map((panel, idx) => (
            <div key={idx} className={`intro-panel${activeTab === idx ? " is-active" : ""}`}
                 id={TABS[idx].panel} role="tabpanel">
              <ul className="intro-ministers__grid">
                {panel.map((person, pidx) => (
                  <MinisterCard key={`${person.name}-${pidx}`} person={person} onOpen={setSelectedPerson} />
                ))}
              </ul>
            </div>
          ))}
        </div>
      </section>

      {selectedPerson && <MinisterModal person={selectedPerson} onClose={() => setSelectedPerson(null)} />}
    </>
  );
}
