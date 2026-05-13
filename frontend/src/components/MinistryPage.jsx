import { useEffect, useMemo, useState } from "react";
import MinistryYangyuk from "./ministry_components/MinistryYangyuk";
import MinistrySmallGroup from "./ministry_components/MinistrySmallGroup";
import MinistryFamily from "./ministry_components/MinistryFamily";
import MinistryMission from "./ministry_components/MinistryMission";
import MinistryScholarship from "./ministry_components/MinistryScholarship";
import MinistryDanielSchool from "./ministry_components/MinistryDanielSchool";
import MinistryLoveToronto from "./ministry_components/MinistryLoveToronto";
import "./css/SubPage.css";
import "./css/MinistryPage.css";

const LNB_ITEMS = [
  { label: "양육", key: "ministry01", href: "/ministry#ministry01" },
  { label: "소그룹", key: "ministry02", href: "/ministry#ministry02" },
  { label: "가정", key: "ministry03", href: "/ministry#ministry03" },
  { label: "선교", key: "ministry04", href: "/ministry#ministry04" },
  { label: "장학", key: "ministry05", href: "/ministry#ministry05" },
  { label: "다니엘한글문화학교", key: "ministry06", href: "/ministry#ministry06" },
  { label: "러브토론토", key: "ministry07", href: "/ministry#ministry07" },
];

const COMPONENT_BY_KEY = {
  ministry01: MinistryYangyuk,
  ministry02: MinistrySmallGroup,
  ministry03: MinistryFamily,
  ministry04: MinistryMission,
  ministry05: MinistryScholarship,
  ministry06: MinistryDanielSchool,
  ministry07: MinistryLoveToronto,
};

function getKeyFromHash(hash) {
  const key = (hash || "").replace("#", "");
  return COMPONENT_BY_KEY[key] ? key : "ministry01";
}

function SubVisual({ title }) {
  return (
    <section className="sub-visual" aria-label="사역 서브 비주얼">
      <div className="sub-visual__bg" aria-hidden="true">
        <figure className="sub-visual__bg-img ministry-bg" />
      </div>
      <div className="sub-visual__ellipse" aria-hidden="true">
        <img src="/images/main/main-visual-ellipse.svg" alt="" />
      </div>
      <div className="sub-visual__cont">
        <nav className="sub-visual__lnb" aria-label="현재 위치">
          <a className="sub-visual__lnb-home" href="/" aria-label="홈">
            <img src="/images/common/ic-nav-w.svg" alt="" />
          </a>
          <span className="sub-visual__lnb-sep" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
              <path d="M3.46447 1.46447L7 5L3.46447 8.53553" stroke="white" strokeOpacity="0.2" strokeWidth="2" strokeLinecap="round" />
            </svg>
          </span>
          <span className="sub-visual__lnb-text">사역</span>
        </nav>
        <h2 className="sub-visual__title">{title}</h2>
      </div>
      <div className="sub-visual__scroll-down" aria-hidden="true">
        <i />
        <span>SCROLL DOWN</span>
      </div>
    </section>
  );
}

function SubLnb({ activeKey }) {
  return (
    <div className="lnb-wrap">
      <nav className="lnb" aria-label="사역 메뉴">
        {LNB_ITEMS.map((item, idx) => (
          <a
            key={item.label}
            className={`lnb__btn${activeKey === item.key ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
            href={item.href}
            {...(item.external ? { target: "_blank", rel: "noopener noreferrer" } : {})}
          >
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

export default function MinistryPage() {
  const [activeKey, setActiveKey] = useState(() => getKeyFromHash(window.location.hash));

  useEffect(() => {
    const onHashChange = () => {
      setActiveKey(getKeyFromHash(window.location.hash));
    };

    window.addEventListener("hashchange", onHashChange);
    onHashChange();

    return () => window.removeEventListener("hashchange", onHashChange);
  }, []);

  const ActiveComponent = useMemo(() => COMPONENT_BY_KEY[activeKey] || MinistryYangyuk, [activeKey]);
  const activeLabel = useMemo(() => LNB_ITEMS.find((item) => item.key === activeKey)?.label ?? "사역", [activeKey]);

  return (
    <>
      <SubVisual title={activeLabel} />
      <div className="sub-content" id="content">
        <SubLnb activeKey={activeKey} />
        <ActiveComponent />
      </div>
    </>
  );
}
