import { useState, useEffect } from "react";
import "./css/IntroMinisters.css";

const TABS_BY_LANGUAGE = {
  kr: ["목회자", "시무장로", "직원"],
  en: ["Pastors", "Elders", "Staff"],
};

const INTRO_MINISTERS_COPY = {
  kr: {
    heading: "섬기는 분들",
    tabAriaLabel: "섬기는 분들 탭",
    modalCloseAria: "팝업 닫기",
    detailButton: "자세히 보기",
    defaultCategory: "목회자",
  },
  en: {
    heading: "Serving Team",
    tabAriaLabel: "Serving team tabs",
    modalCloseAria: "Close dialog",
    detailButton: "View details",
    defaultCategory: "Pastors",
  },
};

const ROLE_LABELS = {
  목사: { kr: "목사", en: "Pastor" },
  전도사: { kr: "전도사", en: "Minister" },
  장로: { kr: "장로", en: "Elder" },
  사무간사: { kr: "사무간사", en: "Administrative Staff" },
  음향간사: { kr: "음향간사", en: "Audio Staff" },
  미디어간사: { kr: "미디어간사", en: "Media Staff" },
  간사: { kr: "간사", en: "Staff" },
};

const CATEGORY_LABELS = {
  목회자: { kr: "목회자", en: "Pastors" },
  장로: { kr: "장로", en: "Elders" },
  간사: { kr: "간사", en: "Staff" },
};



function buildPanels(members) {
  const toImgUrl = (pic) =>
    !pic ? "" : pic.startsWith("http") || pic.startsWith("/") ? pic : `/uploads/members/${pic}`;
  const ministers = members.filter((m) => m.category === "목회자").map((m) => ({
    name: m.name, name_en: m.name_en || m.name,
    role: m.title || m.role || "목사",
    img: toImgUrl(m.picture),
    email: m.email || null,
    position: m.position || null,
    category: m.category || null,
    tags: String(m.tags || "").split("\n").map((t) => t.trim()).filter(Boolean),
    tags_en: String(m.tags_en || "").split("\n").map((t) => t.trim()).filter(Boolean),
    hasDetail: true,
  }));
  const elders = members.filter((m) => m.category === "장로").map((m) => ({
    name: m.name, name_en: m.name_en || m.name,
    role: m.title || "장로",
    img: toImgUrl(m.picture),
  }));
  const staff = members.filter((m) => m.category === "간사").map((m) => ({
    name: m.name, name_en: m.name_en || m.name,
    role: m.title || "간사",
    img: toImgUrl(m.picture),
  }));
  return [ministers, elders, staff];
}

function getLocalizedLabel(label, language, dictionary) {
  const item = dictionary[label];
  if (!item) {
    return label;
  }
  return item[language] || item.kr;
}

function getDisplayName(person, language) {
  return language === "en" ? (person.name_en || person.name) : person.name;
}

function MinisterCard({ person, onOpen, language, copy }) {
  const roleLabel = getLocalizedLabel(person.role, language, ROLE_LABELS);
  const displayName = getDisplayName(person, language);

  return (
    <li className="minister-card" onClick={person.hasDetail ? () => onOpen(person) : undefined}
        role={person.hasDetail ? "button" : undefined} tabIndex={person.hasDetail ? 0 : undefined}
        onKeyDown={person.hasDetail ? (e) => { if (e.key === "Enter" || e.key === " ") { e.preventDefault(); onOpen(person); } } : undefined}  data-ani="top">
      <div className="minister-card__thumb">
        <img src={person.img} alt={`${roleLabel} ${displayName}`} />
        {person.hasDetail && (
          <div className="minister-card__hover">
            <div className="minister-card__hover-btn">
              <i /><span>{copy.detailButton}</span>
            </div>
          </div>
        )}
      </div>
      <div className="minister-card__body">
        <p className="minister-card__role">{roleLabel}</p>
        <p className="minister-card__name">{displayName}</p>
      </div>
    </li>
  );
}

function MinisterModal({ person, onClose, language, copy }) {
  const isEn = language === "en";
  const tags = (isEn ? person.tags_en : person.tags) ?? [];
  const positions = person.position ? [person.position] : [person.role];
  const email = person.email;
  const roleLabel = getLocalizedLabel(person.role, language, ROLE_LABELS);
  const categoryLabel = getLocalizedLabel(person.category || "목회자", language, CATEGORY_LABELS);
  const displayName = getDisplayName(person, language);

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
        <button className="minister-modal__close" type="button" aria-label={copy.modalCloseAria} onClick={onClose} />
        <div className="minister-modal__photo">
          <img src={person.img} alt={`${roleLabel} ${displayName}`} />
        </div>
        <div className="minister-modal__cont">
          <div className="minister-modal__head">
            <div>
              <p className="minister-modal__category">{categoryLabel || copy.defaultCategory}</p>
              <h4 className="minister-modal__name" id="ministerModalName">{displayName} {roleLabel}</h4>
            </div>
            {tags.length > 0 && (
              <div className="minister-modal__tags">
                {tags.map((tag, i) => <span key={i} className="minister-modal__tag">{tag}</span>)}
              </div>
            )}
            {email && (
              <a className="minister-modal__email" href={`mailto:${email}`}>{email}</a>
            )}
          </div>
          <div className="minister-modal__divider" aria-hidden="true" />
          <ul className="minister-modal__position" data-list="dot">
            {positions.map((pos, i) => <li key={i}>{getLocalizedLabel(pos, language, ROLE_LABELS)}</li>)}
          </ul>
        </div>
      </div>
    </div>
  );
}

export default function IntroMinisters({ members = [], language = "kr" }) {
  const [activeTab, setActiveTab] = useState(0);
  const [selectedPerson, setSelectedPerson] = useState(null);
  const copy = INTRO_MINISTERS_COPY[language] || INTRO_MINISTERS_COPY.kr;
  const tabs = (TABS_BY_LANGUAGE[language] || TABS_BY_LANGUAGE.kr).map((label, index) => ({
    label,
    panel: `ministerPanel-${index}`,
  }));
  const panels = buildPanels(members);

  return (
    <>
      <section id="introduction03" className="intro-ministers">
        <div className="wrap">
          <div className="intro-ministers__head" >
            <h3 data-heading="5xl">{copy.heading}</h3>
            <div className="tab-menu intro-ministers__tab" role="tablist" aria-label={copy.tabAriaLabel}>
              {tabs.map((tab, idx) => (
                <button
                  key={tab.label}
                  className={`tab-btn${idx === tabs.length - 1 ? " tab-btn--sep" : ""}${activeTab === idx ? " is-active" : ""}`}
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
                 id={tabs[idx].panel} role="tabpanel">
              <ul className="intro-ministers__grid">
                {panel.map((person, pidx) => (
                  <MinisterCard key={`${person.name}-${pidx}`} person={person} onOpen={setSelectedPerson} language={language} copy={copy} />
                ))}
              </ul>
            </div>
          ))}
        </div>
      </section>

      {selectedPerson && <MinisterModal person={selectedPerson} onClose={() => setSelectedPerson(null)} language={language} copy={copy} />}
    </>
  );
}
