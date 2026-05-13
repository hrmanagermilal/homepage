import { useEffect, useRef, useState } from "react";
import IntroVision from "./introduction_components/IntroVision";
import IntroPastor from "./introduction_components/IntroPastor";
import IntroMinisters from "./introduction_components/IntroMinisters";
import IntroPartner from "./introduction_components/IntroPartner";
import "./css/IntroductionPage.css";
import "./css/SubPage.css";

const LNB_ITEMS = [
  { label: "교회비전", href: "#introduction01" },
  { label: "담임목사", href: "#introduction02" },
  { label: "섬기는 분들", href: "#introduction03" },
  { label: "함께하는 교회", href: "#introduction04" },
];

const HERO_COPY = {
  kr: {
    breadcrumb: "교회 소개",
  },
  en: {
    breadcrumb: "Introduction",
  },
};

function SubVisual({ heroLanguage, setHeroLanguage }) {
  const heroCopy = HERO_COPY[heroLanguage];

  return (
    <section className="sub-visual sub-visual--intro" aria-label="Introduction 서브 비주얼" data-snap-section="true">
      <div className="sub-visual__bg" aria-hidden="true">
        <figure className="sub-visual__bg-img intro-bg" />
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
              <path d="M3.46447 1.46447L7 5L3.46447 8.53553" stroke="white" strokeOpacity="0.2" strokeWidth="2" strokeLinecap="round"/>
            </svg>
          </span>
          <span className="sub-visual__lnb-text">Introduction</span>
        </nav>
        <h2 className="sub-visual__title">{heroCopy.breadcrumb}</h2>
        <div className="sub-visual__lang-switch" role="group" aria-label="언어 선택">
          <button
            type="button"
            className={`sub-visual__lang-btn${heroLanguage === "kr" ? " is-active" : ""}`}
            aria-pressed={heroLanguage === "kr"}
            onClick={() => setHeroLanguage("kr")}
          >
            KR
          </button>
          <span className="sub-visual__lang-divider" aria-hidden="true" />
          <button
            type="button"
            className={`sub-visual__lang-btn${heroLanguage === "en" ? " is-active" : ""}`}
            aria-pressed={heroLanguage === "en"}
            onClick={() => setHeroLanguage("en")}
          >
            EN
          </button>
        </div>
      </div>
      <div className="sub-visual__scroll-down" aria-hidden="true">
        <i /><span>SCROLL DOWN</span>
      </div>
    </section>
  );
}

function SubLnb() {
  return (
    <div className="lnb-wrap">
      <nav className="lnb" aria-label="Introduction 메뉴">
        {LNB_ITEMS.map((item, idx) => (
          <a key={idx} className={`lnb__btn${idx === 0 ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
             href={item.href}>
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

export default function IntroductionPage({ togetherItems = [], members = [], visionStatements = [] }) {
  const containerRef = useRef(null);
  const [heroLanguage, setHeroLanguage] = useState("kr");

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

  return (
    <div ref={containerRef}>
      <SubVisual heroLanguage={heroLanguage} setHeroLanguage={setHeroLanguage} />
      <div className="sub-content" id="content">
        <SubLnb />
        <div data-snap-section="true">
          <IntroVision visionStatements={visionStatements} language={heroLanguage} />
        </div>
        <div data-snap-section="true">
          <IntroPastor language={heroLanguage} />
        </div>
        <div data-snap-section="true">
          <IntroMinisters members={members} language={heroLanguage} />
        </div>
        <section id="introduction04" data-snap-section="true">
          <IntroPartner togetherItems={togetherItems} language={heroLanguage} />
        </section>
      </div>
    </div>
  );
}
