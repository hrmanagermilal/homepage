import { useEffect, useMemo, useRef, useState } from "react";
import { FEATURES } from "../config/features";
import "./css/SubPage.css";
import "./css/ObituaryPage.css";
import ObituarySubVisual from "./obituary_components/ObituarySubVisual";
import ObituarySearchForm from "./obituary_components/ObituarySearchForm";
import ObituaryCard from "./obituary_components/ObituaryCard";
import ObituaryPagination from "./obituary_components/ObituaryPagination";

const OBITUARY_DATA = [
  {
    id: 1,
    title: "박주희 집사(김주환 집사)<br>모친 소천(영광 2순)",
    description: "강혜숙 권사님(딸: 박주희 집사, 사위: 김주환 집사) 께서 2026년 4월 17일(금) 향년 84세로",
    date: "2026. 04. 17",
  },
  {
    id: 2,
    title: "이효숙 성도 부친 소천<br>(청장년 1순)",
    description: "이무남 성도님(딸: 이효숙 성도)께서 2026년 4월 12일(주일), 향년 82세로 하나님의 부르심을 받으셨습니다.",
    date: "2026. 04. 01",
  },
  {
    id: 3,
    title: "이진아(윤석원)집사 부친 소천<br>(온유 4순)",
    description: "이건대 장로님(딸: 이진아 집사, 사위: 윤석원 집사)께서 2026년 2월 19일(목), 향년 81세로 하나님의 부르심을 받으셨습니다.",
    date: "2026. 03. 08",
  },
  {
    id: 4,
    title: "김일환(이순녀)집사 소천(모세회)",
    description: "김일환 집사님(이순녀 명예권사)께서 2026년 3월 2일(월) 오후 1시, 향년 98세로 하나님의 부름을 받으셨습니다.",
    date: "2026. 03. 03",
  },
  {
    id: 5,
    title: "서예원 집사 부친 소천(충성 5순)",
    description: "서재호 성도님(딸: 서예원 집사)께서 2026년 2월 19(목) 오전 6시 20분, 향년 84세로 하나님의 부르심을 받으셨습니다.",
    date: "2026. 02. 18",
  },
  {
    id: 6,
    title: "조양임 집사(심택)모친 소천<br>(기쁨 4순)",
    description: "유명자 집사님(딸: 조양임 집사, 사위: 심택 집사)께서 2026년 2월 15일(주일), 향년 85세로 하나님의 부르심을 받으셨습니다.",
    date: "2026. 02. 15",
  },
];

const ITEMS_PER_PAGE = 8;

export default function ObituaryPage() {
  const containerRef = useRef(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [searchQuery, setSearchQuery] = useState("");

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

  const filteredData = useMemo(() => {
    if (!searchQuery) {
      return OBITUARY_DATA;
    }
    return OBITUARY_DATA.filter(
      (item) =>
        item.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.description.toLowerCase().includes(searchQuery.toLowerCase())
    );
  }, [searchQuery]);

  const totalPages = Math.ceil(filteredData.length / ITEMS_PER_PAGE);
  const paginatedData = useMemo(() => {
    const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
    return filteredData.slice(startIdx, startIdx + ITEMS_PER_PAGE);
  }, [filteredData, currentPage]);

  const handleSearch = () => {
    setCurrentPage(1);
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);
    document.getElementById("content")?.scrollIntoView({ behavior: "smooth", block: "start" });
  };

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <ObituarySubVisual />
      </div>
      <div className="sub-content" id="content" data-snap-section="true">
        <div className="lnb-wrap" data-ani="top">
          <nav className="lnb" aria-label="소식 메뉴">
            <button className="lnb__btn" onClick={() => { window.history.pushState({}, "", "/news/notice"); window.dispatchEvent(new Event("locationchange")); }}>공지</button>
            <button className="lnb__btn lnb__btn--sep is-active" onClick={() => { window.history.pushState({}, "", "/news/obituary"); window.dispatchEvent(new Event("locationchange")); }}>부고</button>
          </nav>
        </div>
        <section className="obituary">
          <div className="wrap">
            <h3 className="obituary-heading" data-heading="5xl">
              부고
            </h3>

            <div className="obituary-top">
              <p className="obituary-count">
                총 <strong>{filteredData.length}</strong>건
              </p>
              <ObituarySearchForm
                searchQuery={searchQuery}
                onSearchChange={setSearchQuery}
                onSearch={handleSearch}
              />
            </div>

            <ul className="obituary-list" data-grid="4">
              {paginatedData.map((item) => (
                <ObituaryCard
                  key={item.id}
                  id={item.id}
                  title={item.title}
                  description={item.description}
                  date={item.date}
                />
              ))}
            </ul>

            <ObituaryPagination currentPage={currentPage} totalPages={totalPages} onPageChange={handlePageChange} />
          </div>
        </section>
      </div>
    </div>
  );
}
