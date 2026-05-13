import { useEffect, useMemo, useRef, useState } from "react";
import MinistryYangyuk from "./ministry_components/MinistryYangyuk";
import MinistrySmallGroup from "./ministry_components/MinistrySmallGroup";
import MinistryFamily from "./ministry_components/MinistryFamily";
import MinistryMission from "./ministry_components/MinistryMission";
import MinistryScholarship from "./ministry_components/MinistryScholarship";
import MinistryGospelProject from "./ministry_components/MinistryGospelProject";
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
  { label: "가스펠프로젝트", key: "ministry08", href: "/ministry#ministry08" },
  { label: "다니엘한글문화학교", key: "ministry06", href: "/ministry#ministry06" },
  { label: "러브토론토", key: "ministry07", href: "/ministry#ministry07" },
];

const COMPONENT_BY_KEY = {
  ministry01: MinistryYangyuk,
  ministry02: MinistrySmallGroup,
  ministry03: MinistryFamily,
  ministry04: MinistryMission,
  ministry05: MinistryScholarship,
  ministry08: MinistryGospelProject,
  ministry06: MinistryDanielSchool,
  ministry07: MinistryLoveToronto,
};

const SUBTITLE_BY_KEY = {
  ministry01: "우리는 밀알 공동체입니다.",
  ministry02: "함께 말씀으로 자라는 공동체",
  ministry03: "당신의 첫 제자는 당신의 자녀입니다.",
  ministry04: "복음을 들고 세상으로",
  ministry05: "다음세대를 세우는 믿음의 투자",
  ministry08: "체계적인 성경적 가치관 확립",
  ministry06: "하나님의 자녀가 하나님의 자녀에게",
  ministry07: "도시를 사랑으로 섬기는 손길",
};

function getKeyFromHash(hash) {
  const key = (hash || "").replace("#", "");
  return COMPONENT_BY_KEY[key] ? key : "ministry01";
}

function SubVisual({ title, activeKey }) {
  const isDaniel = title === "다니엘한글문화학교";
  const isGajeong = title === "가정";
  const isYangyuk = title === "양육";
  const isSeonkyo = title === "선교";
  const isJanghak = title === "장학";
  const isSogroup = title === "소그룹";
  const isGospel = activeKey === "ministry08";
  const subtitle = SUBTITLE_BY_KEY[activeKey] || "";
  const bgClass = isGospel ? "ministry-bg-gospel" : isDaniel ? "ministry-bg-daniel" : isGajeong ? "ministry-bg-gajeong" : isYangyuk ? "ministry-bg-yangyuk" : isSeonkyo ? "ministry-bg-seonkyo" : isJanghak ? "ministry-bg-janghak" : isSogroup ? "ministry-bg-sogroup" : "ministry-bg";
  return (
    <section className="sub-visual" aria-label="사역 서브 비주얼">
      <div className="sub-visual__bg" aria-hidden="true">
        <figure className={`sub-visual__bg-img ${bgClass}`} />
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
        {subtitle ? <p className="sub-visual__subtitle">{subtitle}</p> : null}
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

const BG_IMAGES = [
  "/images/sub/03-ministry/sub-visual-bg.jpg",
  "/images/sub/visual/gospel-intro.jpg",
  "/images/sub/visual/sub-visual0307.jpg",
  "/images/sub/visual/sub-visual0303.jpg",
  "/images/sub/visual/sub-visual0306.jpg",
  "/images/sub/visual/sub-visual0207.jpg",
  "/images/sub/visual/sub-visual0305.jpg",
  "/images/sub/visual/sub-visual0302.jpg",
];

export default function MinistryPage() {
  const containerRef = useRef(null);
  const [activeKey, setActiveKey] = useState(() => getKeyFromHash(window.location.hash));

  useEffect(() => {
    BG_IMAGES.forEach((src) => {
      const img = new Image();
      img.src = src;
    });
  }, []);

  useEffect(() => {
    const onHashChange = () => {
      setActiveKey(getKeyFromHash(window.location.hash));
    };

    window.addEventListener("hashchange", onHashChange);
    onHashChange();

    return () => window.removeEventListener("hashchange", onHashChange);
  }, []);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) {
      return;
    }

    const isDesktopScrollSnap =
      typeof window !== "undefined" &&
      window.matchMedia("(min-width: 1024px)").matches &&
      window.matchMedia("(pointer: fine)").matches;

    if (!isDesktopScrollSnap) {
      return;
    }

    const sections = Array.from(container.querySelectorAll("[data-snap-section='true']"));
    if (!sections.length) {
      return;
    }

    let isAnimating = false;
    let wheelLockUntil = 0;

    const getClosestSectionIndex = () => {
      const viewportMid = window.innerHeight / 2;
      let bestIndex = 0;
      let bestDistance = Number.POSITIVE_INFINITY;

      sections.forEach((section, index) => {
        const rect = section.getBoundingClientRect();
        const sectionMid = rect.top + rect.height / 2;
        const distance = Math.abs(sectionMid - viewportMid);
        if (distance < bestDistance) {
          bestDistance = distance;
          bestIndex = index;
        }
      });

      return bestIndex;
    };

    const moveToSection = (index) => {
      if (index < 0 || index >= sections.length) {
        return;
      }

      isAnimating = true;
      sections[index].scrollIntoView({ behavior: "smooth", block: "start" });
      window.setTimeout(() => {
        isAnimating = false;
      }, 700);
    };

    const onWheel = (event) => {
      const now = Date.now();
      if (isAnimating || now < wheelLockUntil) {
        event.preventDefault();
        return;
      }

      if (Math.abs(event.deltaY) < 8) {
        return;
      }

      const currentIndex = getClosestSectionIndex();
      const direction = event.deltaY > 0 ? 1 : -1;
      const nextIndex = Math.max(0, Math.min(sections.length - 1, currentIndex + direction));

      if (nextIndex === currentIndex) {
        return;
      }

      event.preventDefault();
      wheelLockUntil = now + 500;
      moveToSection(nextIndex);
    };

    window.addEventListener("wheel", onWheel, { passive: false });
    return () => {
      window.removeEventListener("wheel", onWheel);
    };
  }, []);

  const ActiveComponent = useMemo(() => COMPONENT_BY_KEY[activeKey] || MinistryYangyuk, [activeKey]);
  const activeLabel = useMemo(() => LNB_ITEMS.find((item) => item.key === activeKey)?.label ?? "사역", [activeKey]);

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <SubVisual title={activeLabel} activeKey={activeKey} />
      </div>
      <div className="sub-content" id="content" data-snap-section="true">
        <SubLnb activeKey={activeKey} />
        <ActiveComponent />
      </div>
    </div>
  );
}
