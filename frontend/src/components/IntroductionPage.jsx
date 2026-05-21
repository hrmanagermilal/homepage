import { useEffect, useRef, useState } from "react";
import { FEATURES } from "../config/features";
import IntroVision from "./introduction_components/IntroVision";
import IntroPastor from "./introduction_components/IntroPastor";
import IntroMinisters from "./introduction_components/IntroMinisters";
import IntroPartner from "./introduction_components/IntroPartner";
import "./css/IntroductionPage.css";
import "./css/SubPage.css";

const LNB_ITEMS = {
  kr: [
    { label: "교회비전", href: "#introduction01" },
    { label: "담임목사", href: "#introduction02" },
    { label: "섬기는 분들", href: "#introduction03" },
    { label: "함께하는 교회", href: "#introduction04" },
  ],
  en: [
    { label: "Church Vision", href: "#introduction01" },
    { label: "Senior Pastor", href: "#introduction02" },
    { label: "Serving Team", href: "#introduction03" },
    { label: "Partner Churches", href: "#introduction04" },
  ],
};

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
    <section className="sub-visual sub-visual--intro" aria-label="Introduction 서브 비주얼">
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

function SubLnb({ heroLanguage }) {
  const items = LNB_ITEMS[heroLanguage] || LNB_ITEMS.kr;

  return (
    <div className="lnb-wrap" data-ani="top">
      <nav className="lnb" aria-label={heroLanguage === "en" ? "Introduction section tabs" : "Introduction 메뉴"}>
        {items.map((item, idx) => (
          <a key={idx} className={`lnb__btn${idx === 0 ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
             href={item.href}>
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

export default function IntroductionPage({ togetherItems = [], members = [], visionStatements = [], pastorIntroduction = null }) {
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

    // Returns the index of the section currently in view.
    // For tall sections, this is the last section whose top has entered the viewport.
    // Threshold accounts for the 90rem scroll-margin-top used on sub-sections.
    const getActiveSectionIndex = () => {
      const remPx = parseFloat(getComputedStyle(document.documentElement).fontSize);
      const threshold = remPx * 92; // slightly above the 90rem scroll-margin-top
      let activeIndex = 0;
      sections.forEach((section, index) => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= threshold) {
          activeIndex = index;
        }
      });
      return activeIndex;
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

      const direction = event.deltaY > 0 ? 1 : -1;
      const currentIndex = getActiveSectionIndex();
      const currentSection = sections[currentIndex];
      const rect = currentSection.getBoundingClientRect();

      // Scrolling down but section bottom is not yet fully visible → scroll one viewport down (or to section bottom if closer)
      if (direction > 0 && rect.bottom > window.innerHeight + 4) {
        event.preventDefault();
        wheelLockUntil = now + 500;
        isAnimating = true;
        const scrollAmount = Math.min(window.innerHeight, rect.bottom - window.innerHeight);
        window.scrollTo({ top: window.scrollY + scrollAmount, behavior: "smooth" });
        window.setTimeout(() => { isAnimating = false; }, 700);
        return;
      }

      // Scrolling up but section top is not yet fully visible → scroll one viewport up (or to section top if closer)
      if (direction < 0 && rect.top < -4) {
        event.preventDefault();
        wheelLockUntil = now + 500;
        isAnimating = true;
        const scrollAmount = Math.min(window.innerHeight, -rect.top);
        window.scrollTo({ top: window.scrollY - scrollAmount, behavior: "smooth" });
        window.setTimeout(() => { isAnimating = false; }, 700);
        return;
      }

      const nextIndex = Math.max(0, Math.min(sections.length - 1, currentIndex + direction));

      if (nextIndex === currentIndex) {
        return;
      }

      event.preventDefault();
      wheelLockUntil = now + 500;
      moveToSection(nextIndex);
    };

    if (FEATURES.SCROLL_SNAP_ENABLED) {
      window.addEventListener("wheel", onWheel, { passive: false });
      return () => {
        window.removeEventListener("wheel", onWheel);
      };
    }
    return () => {};
  }, []);

  console.log('visionStatements', visionStatements);

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <SubVisual heroLanguage={heroLanguage} setHeroLanguage={setHeroLanguage} />
      </div>
      <div className="sub-content" id="content" data-snap-section="true">
        <SubLnb heroLanguage={heroLanguage} />
        <IntroVision visionStatements={visionStatements} language={heroLanguage} />
      </div>
      <div data-snap-section="true" style={{ scrollMarginTop: "90rem" }}>
        <IntroPastor pastorData={pastorIntroduction} language={heroLanguage} />
      </div>
      <div data-snap-section="true" style={{ scrollMarginTop: "90rem" }}>
        <IntroMinisters members={members} language={heroLanguage} />
      </div>
      <div data-snap-section="true" style={{ scrollMarginTop: "90rem" }}>
        <section id="introduction04">
          <IntroPartner togetherItems={togetherItems} language={heroLanguage} />
        </section>
      </div>
    </div>
  );
}
