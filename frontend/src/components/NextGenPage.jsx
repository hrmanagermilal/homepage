import { useEffect, useRef, useState } from "react";
import { FEATURES } from "../config/features";
import NextGenDepartment from "./nextgen_components/NextGenDepartment";
import "./css/SubPage.css";
import "./css/NextGenPage.css";

const KEY_TO_TITLE = {
  "young-adults": "청년부",
  "km-youth": "KM 청소년부",
  "em-youth": "EM 청소년부",
  "children": "아동부",
  "kindergarten": "유치부",
  "infants": "영유아부",
};

const NEXTGEN_LNB_ITEMS = [
  { label: "청년부", key: "young-adults", href: "/nextgen#young-adults" },
  { label: "KM 청소년부", key: "km-youth", href: "/nextgen#km-youth" },
  { label: "EM 청소년부", key: "em-youth", href: "/nextgen#em-youth" },
  { label: "아동부", key: "children", href: "/nextgen#children" },
  { label: "유치부", key: "kindergarten", href: "/nextgen#kindergarten" },
  { label: "영유아부", key: "infants", href: "/nextgen#infants" },
];

function getKeyFromHash(hash) {
  const key = (hash || "").replace("#", "");
  return KEY_TO_TITLE[key] ? key : "young-adults";
}

const DEPARTMENT_CONTENT = {
  "청년부": {
    headingTitle: (
      <>
        Milight, Time to Shine. 하나님이여 우리를 돌이키시고
        <br />
        주의 얼굴빛을 비추사 우리가 구원을 얻게 하소서 (시편 80:3)
      </>
    ),
    headingSub: (
      <>
        토론토의 새벽이슬 같은 청년들이 모이면 예배하고,
        <br />
        흩어지면 빛을 발하는 공동체입니다.
      </>
    ),
    worshipTime: "주일 오후 2시",
    worshipLocation: "밀알교회 1층 본당",
    pastorName: "신효성 목사",
    pastorEmail: "rev.shin@milalchurch.com",
    pastorPhoto: "/images/sub/02-next-generation/pastor-photo.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "청년부 카카오톡 채널 추가하기",
    photoAlt: "신효성 목사 사진",
    noticeTitle: "청년부 소식",
    noticeDescription: "청년부의 소식과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
  },
  "KM 청소년부": {
    headingTitle: "KM 청소년부, 믿음 안에서 함께 성장합니다.",
    headingSub: "말씀과 기도로 다음세대가 정체성을 세우고, 건강한 공동체를 경험하도록 돕습니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 2층 청소년부 예배실",
    pastorName: "차승현 목사",
    pastorEmail: "nextgen@milalchurch.com",
    pastorPhoto: "/images/sub/01-introduction/minister-05.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "KM 청소년부 카카오톡 채널 추가하기",
    photoAlt: "KM 청소년부 담당 교역자",
    noticeTitle: "KM 청소년부 소식",
    noticeDescription: "주간 프로그램과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
  },
  "EM 청소년부": {
    headingTitle: "EM Youth, Grounded in the Word.",
    headingSub: "We gather for worship and discipleship, and go out as Christ-centered witnesses in daily life.",
    worshipTime: "주일 오후 1시",
    worshipLocation: "밀알교회 2층 청소년부 예배실",
    pastorName: "조나단 목사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "EM Youth 소식",
    noticeDescription: "프로그램 일정과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-09.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "EM Youth 카카오톡 채널 추가하기",
    photoAlt: "EM Youth 담당 교역자",
  },
  "아동부": {
    headingTitle: "아동부, 예수님을 닮아가는 어린이들",
    headingSub: "예배와 말씀, 활동을 통해 아이들이 즐겁게 하나님을 알아가도록 세웁니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 아동부실",
    pastorName: "김진아 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "아동부 프로그램",
    noticeDescription: "월간 프로그램과 학부모 안내자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-13.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "아동부 카카오톡 채널 추가하기",
    photoAlt: "아동부 담당 교역자",
  },
  "유치부": {
    headingTitle: "유치부, 믿음의 씨앗을 심는 시간",
    headingSub: "아이들의 눈높이에 맞춘 예배와 활동으로 하나님의 사랑을 자연스럽게 배우게 합니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 유치부실",
    pastorName: "김비치 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "유치부 프로그램",
    noticeDescription: "월간 공지사항과 부모교육 자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-12.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "유치부 카카오톡 채널 추가하기",
    photoAlt: "유치부 담당 교역자",
  },
  "영유아부": {
    headingTitle: "영유아부, 사랑 안에서 첫 걸음을",
    headingSub: "부모와 교사가 함께 아이들의 신앙 첫 걸음을 따뜻하게 동행합니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 영유아부실",
    pastorName: "주은지 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "영유아부 프로그램",
    noticeDescription: "월간 프로그램과 부모 양육 안내자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-14.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "영유아부 카카오톡 채널 추가하기",
    photoAlt: "영유아부 담당 교역자",
  },
};

const SUBTITLE_BY_TITLE = {
  "청년부": "Milight, Time to Shine.",
  "KM 청소년부": "믿음 안에서 함께 성장합니다.",
  "EM 청소년부": "Grounded in the Word.",
  "아동부": "예수님을 닮아가는 어린이들",
  "유치부": "믿음의 씨앗을 심는 시간",
  "영유아부": "사랑 안에서 첫 걸음을",
};


function SubVisual({ title }) {
  const isYoungAdults = title === "청년부";
  const isKmOrEm = title === "KM 청소년부" || title === "EM 청소년부";
  const isChildren = title === "아동부";
  const isKindergarten = title === "유치부";
  const isInfantToddler = title === "영유아부";
  const subtitle = SUBTITLE_BY_TITLE[title] || "";
  const bgClass = isYoungAdults ? "nextgen-bg-young" : isKmOrEm ? "nextgen-bg-youth" : isChildren ? "nextgen-bg-children" : isKindergarten ? "nextgen-bg-kindergarten" : isInfantToddler ? "nextgen-bg-infant" : "nextgen-bg";
  return (
    <section className="sub-visual" aria-label="다음세대 서브 비주얼">
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
          <span className="sub-visual__lnb-text">다음세대</span>
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
    <div className="lnb-wrap" data-ani="top">
      <nav className="lnb" aria-label="다음세대 메뉴">
        {NEXTGEN_LNB_ITEMS.map((item, idx) => (
          <a
            key={item.key}
            className={`lnb__btn${activeKey === item.key ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
            href={item.href}
          >
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

function renderTextWithBreaks(text) {
  if (!text) return null;
  const parts = text.split("\n");
  if (parts.length === 1) return text;
  return parts.reduce((acc, part, i) => {
    if (i === 0) return [part];
    return [...acc, <br key={i} />, part];
  }, []);
}

function mapDeptToContent(dept) {
  return {
    headingTitle: renderTextWithBreaks(dept.heading_title) || dept.name,
    headingSub: renderTextWithBreaks(dept.description),
    worshipTime: [dept.worship_day, dept.worship_time].filter(Boolean).join(" "),
    worshipLocation: dept.worship_location || "",
    pastorName: dept.clergy_name || "",
    pastorEmail: dept.pastor_email || "",
    pastorPhoto: dept.image || null,
    photoAlt: dept.clergy_name ? `${dept.clergy_name} 사진` : "",
    kakaoLink: dept.kakao_link || "",
    kakaoLabel: dept.kakao_label || "",
    noticeTitle: dept.notice_title || "",
    noticeDescription: dept.notice_description || "",
    noticeButtonLabel: dept.notice_button_label || "",
    noticeButtonHref: dept.notice_button_href || "#",
  };
}

export default function NextGenPage({ departments = [] }) {
  const containerRef = useRef(null);
  const [activeKey, setActiveKey] = useState(() => getKeyFromHash(window.location.hash));
  const safeTitle = KEY_TO_TITLE[activeKey] || "청년부";
  const apiDept = departments.find(d => d.name === safeTitle);
  const content = apiDept ? mapDeptToContent(apiDept) : DEPARTMENT_CONTENT[safeTitle];

  useEffect(() => {
    const onHashChange = () => setActiveKey(getKeyFromHash(window.location.hash));
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

    if (FEATURES.SCROLL_SNAP_ENABLED) {
      window.addEventListener("wheel", onWheel, { passive: false });
      return () => {
        window.removeEventListener("wheel", onWheel);
      };
    }
    return () => {};
  }, []);

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <SubVisual title={safeTitle} />
      </div>
      <div className="sub-content" id="content" data-snap-section="true">
        <SubLnb activeKey={activeKey} />
        <NextGenDepartment {...content} />
      </div>
    </div>
  );
}
