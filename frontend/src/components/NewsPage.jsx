import { useEffect, useMemo, useRef, useState } from "react";
import { FEATURES } from "../config/features";
import "./css/SubPage.css";
import "./css/NoticePage.css";
import "./css/ObituaryPage.css";
import NoticeSubVisual from "./notice_components/NoticeSubVisual";
import ObituarySubVisual from "./obituary_components/ObituarySubVisual";
import NoticeTable from "./notice_components/NoticeTable";
import NoticeSearch from "./notice_components/NoticeSearch";
import NoticePagination from "./notice_components/NoticePagination";
import ObituarySearchForm from "./obituary_components/ObituarySearchForm";
import ObituaryCard from "./obituary_components/ObituaryCard";
import ObituaryPagination from "./obituary_components/ObituaryPagination";

const NEWS_LNB_ITEMS = [
  { label: "공지", key: "notice", href: "/news#notice" },
  { label: "부고", key: "obituary", href: "/news#obituary" },
];

function getKeyFromHash(hash) {
  const key = (hash || "").replace("#", "");
  return key === "obituary" ? "obituary" : "notice";
}

const NOTICE_DATA = [
  {
    id: 1,
    title: "2026년 1월 주보 및 교회 일정 안내",
    author: "성전관리",
    date: "2026. 01. 01",
    views: 245,
    content: "2026년 새해를 맞이하여 주간 예배 일정 및 각 부서 활동 계획을 공지드립니다.<br><br>주일예배: 오전 11시<br>수요기도회: 오후 7시 30분<br>새벽기도: 오전 5시 30분<br><br>자세한 일정은 첨부된 주보를 참고해주시기 바랍니다.",
  },
  {
    id: 2,
    title: "새해맞이 성령집회 안내",
    author: "행정부",
    date: "2025. 12. 28",
    views: 312,
    content: "2026년 새해를 맞이하여 특별 성령집회를 개최합니다.<br><br>일시: 2026년 1월 1일 ~ 3일<br>시간: 오후 7시 30분<br>장소: 메인 예배당<br><br>모든 성도들의 적극적인 참여를 부탁드립니다.",
  },
  {
    id: 3,
    title: "교회 건축 기금 모금 안내",
    author: "재정부",
    date: "2025. 12. 20",
    views: 189,
    content: "교회 새 건물 건축을 위한 기금 모금을 시작합니다.<br><br>목표액: $500,000<br>모금 기간: 2026년 1월 ~ 12월<br>헌금 방법: 온라인 헌금, 주일예배 헌금, E-Transfer<br><br>더 자세한 정보는 사무실에 문의해주시기 바랍니다.",
  },
  {
    id: 4,
    title: "2026년 사역자 간담회 개최",
    author: "성전관리",
    date: "2025. 12. 15",
    views: 156,
    content: "2026년도 교회 사역 계획 수립을 위한 사역자 간담회를 개최합니다.<br><br>일시: 2025년 12월 22일 (화요일)<br>시간: 오후 7시<br>장소: 교육관<br><br>모든 사역자의 참석을 부탁드립니다.",
  },
  {
    id: 5,
    title: "성탄절 특별예배 안내",
    author: "행정부",
    date: "2025. 12. 10",
    views: 421,
    content: "2025년 성탄절을 맞이하여 특별예배를 드립니다.<br><br>일시: 2025년 12월 25일 오전 10시<br>장소: 메인 예배당<br><br>본 예배 이후 성찬식과 교제의 시간을 갖을 예정입니다. 모든 성도들의 참석을 기원합니다.",
  },
  {
    id: 6,
    title: "2025년 감사절 감사예배 안내",
    author: "성전관리",
    date: "2025. 11. 28",
    views: 267,
    content: "2025년 추수감사절을 맞이하여 감사예배를 드립니다.<br><br>일시: 2025년 11월 27일 오전 11시<br>장소: 메인 예배당<br><br>감사의 물품은 다음과 같습니다: 곡식, 과일, 채소 등<br>감사헌금은 사회복지사업에 사용될 예정입니다.",
  },
  {
    id: 7,
    title: "추수감사절 헌금 안내",
    author: "재정부",
    date: "2025. 11. 20",
    views: 198,
    content: "추수감사절 감사헌금 안내드립니다.<br><br>헌금은 다음과 같은 용도로 사용됩니다:<br>- 지역사회 나눔 사업<br>- 선교사 지원<br>- 어려운 이웃 돕기<br><br>많은 참여 부탁드립니다.",
  },
  {
    id: 8,
    title: "교회 건물 리모델링 공사 안내",
    author: "성전관리",
    date: "2025. 11. 15",
    views: 334,
    content: "교회 건물의 노후 시설 개선을 위한 리모델링 공사를 시작합니다.<br><br>공사 기간: 2025년 11월 20일 ~ 2026년 2월 28일<br>공사 구간: 교육관, 복도, 주차장<br><br>공사 기간 중 불편을 드릴 수 있으니 양해 부탁드립니다.",
  },
  {
    id: 9,
    title: "미션 여행 팀 모집",
    author: "선교부",
    date: "2025. 11. 05",
    views: 145,
    content: "2026년 선교 여행에 참여할 팀을 모집합니다.<br><br>목적지: 베트남<br>일정: 2026년 6월 15일 ~ 21일<br>인원: 15명 (선착순)<br><br>자세한 정보는 선교부에 문의하시기 바랍니다.",
  },
  {
    id: 10,
    title: "2025년 겨울 성경학교 등록 안내",
    author: "교육부",
    date: "2025. 10. 28",
    views: 289,
    content: "2025년 겨울 성경학교 등록을 받고 있습니다.<br><br>일정: 2025년 12월 29일 ~ 2026년 1월 3일<br>대상: 미취학아동 ~ 중등부<br>시간: 오전 10시 ~ 오후 12시<br><br>온라인 등록: www.milalchurch.ca<br>문의: 교육부 (416) 000-0000",
  },
];

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

function SubLnb({ activeKey }) {
  return (
    <div className="lnb-wrap" data-ani="top">
      <nav className="lnb" aria-label="소식 메뉴">
        {NEWS_LNB_ITEMS.map((item, idx) => (
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

export default function NewsPage() {
  const containerRef = useRef(null);
  const [activeKey, setActiveKey] = useState(() => getKeyFromHash(window.location.hash));

  // Notice state
  const [noticeSearch, setNoticeSearch] = useState("");
  const [noticeSortOrder, setNoticeSortOrder] = useState("newest");
  const [noticeCurrentPage, setNoticeCurrentPage] = useState(1);

  // Obituary state
  const [obituarySearch, setObituarySearch] = useState("");
  const [obituaryCurrentPage, setObituaryCurrentPage] = useState(1);

  useEffect(() => {
    const onHashChange = () => {
      setActiveKey(getKeyFromHash(window.location.hash));
    };
    window.addEventListener("hashchange", onHashChange);
    return () => window.removeEventListener("hashchange", onHashChange);
  }, []);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) return;

    const isDesktopScrollSnap =
      typeof window !== "undefined" &&
      window.matchMedia("(min-width: 1024px)").matches &&
      window.matchMedia("(pointer: fine)").matches;

    if (!isDesktopScrollSnap) return;

    const sections = Array.from(container.querySelectorAll("[data-snap-section='true']"));
    if (!sections.length) return;

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
      if (index < 0 || index >= sections.length) return;
      isAnimating = true;
      sections[index].scrollIntoView({ behavior: "smooth", block: "start" });
      window.setTimeout(() => { isAnimating = false; }, 700);
    };

    const onWheel = (event) => {
      const now = Date.now();
      if (isAnimating || now < wheelLockUntil) {
        event.preventDefault();
        return;
      }
      if (Math.abs(event.deltaY) < 8) return;

      const currentIndex = getClosestSectionIndex();
      const direction = event.deltaY > 0 ? 1 : -1;
      const nextIndex = Math.max(0, Math.min(sections.length - 1, currentIndex + direction));
      if (nextIndex === currentIndex) return;

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
    return () => {}
  }, []);

  // Notice computed
  const noticeFiltered = useMemo(() => {
    return NOTICE_DATA.filter((item) =>
      item.title.toLowerCase().includes(noticeSearch.toLowerCase()) ||
      item.author.toLowerCase().includes(noticeSearch.toLowerCase())
    );
  }, [noticeSearch]);

  const noticeSorted = useMemo(() => {
    const data = [...noticeFiltered];
    if (noticeSortOrder === "newest") return data.reverse();
    if (noticeSortOrder === "oldest") return data;
    if (noticeSortOrder === "views") return data.sort((a, b) => b.views - a.views);
    return data;
  }, [noticeFiltered, noticeSortOrder]);

  const noticeTotalPages = Math.ceil(noticeSorted.length / ITEMS_PER_PAGE);
  const noticePaginated = useMemo(() => {
    const startIdx = (noticeCurrentPage - 1) * ITEMS_PER_PAGE;
    return noticeSorted.slice(startIdx, startIdx + ITEMS_PER_PAGE);
  }, [noticeSorted, noticeCurrentPage]);

  const handleNoticeSearch = (query) => { setNoticeSearch(query); setNoticeCurrentPage(1); };
  const handleNoticeSortChange = (value) => { setNoticeSortOrder(value); setNoticeCurrentPage(1); };
  const handleNoticePageChange = (page) => {
    setNoticeCurrentPage(page);
    document.getElementById("content")?.scrollIntoView({ behavior: "smooth", block: "start" });
  };
  const handleNoticeRowClick = (id) => {
    window.history.pushState({}, "", `/news/notice/${id}`);
    window.dispatchEvent(new Event("locationchange"));
  };

  // Obituary computed
  const obituaryFiltered = useMemo(() => {
    if (!obituarySearch) return OBITUARY_DATA;
    return OBITUARY_DATA.filter(
      (item) =>
        item.title.toLowerCase().includes(obituarySearch.toLowerCase()) ||
        item.description.toLowerCase().includes(obituarySearch.toLowerCase())
    );
  }, [obituarySearch]);

  const obituaryTotalPages = Math.ceil(obituaryFiltered.length / ITEMS_PER_PAGE);
  const obituaryPaginated = useMemo(() => {
    const startIdx = (obituaryCurrentPage - 1) * ITEMS_PER_PAGE;
    return obituaryFiltered.slice(startIdx, startIdx + ITEMS_PER_PAGE);
  }, [obituaryFiltered, obituaryCurrentPage]);

  const handleObituarySearch = () => { setObituaryCurrentPage(1); };
  const handleObituaryPageChange = (page) => {
    setObituaryCurrentPage(page);
    document.getElementById("content")?.scrollIntoView({ behavior: "smooth", block: "start" });
  };

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        {activeKey === "notice" ? <NoticeSubVisual /> : <ObituarySubVisual />}
      </div>
      <div className="sub-content" id="content" data-snap-section="true">
        <SubLnb activeKey={activeKey} />

        {activeKey === "notice" ? (
          <section className="notice">
            <div className="wrap-narrow">
              <div className="notice-top">
                <div className="notice-count">
                  총 <strong>{noticeFiltered.length}</strong>개
                </div>
                <div className="notice-controls">
                  <div className="notice-sort">
                    <select
                      className="notice-sort__select"
                      value={noticeSortOrder}
                      onChange={(e) => handleNoticeSortChange(e.target.value)}
                      aria-label="정렬 순서"
                    >
                      <option value="newest">최신순</option>
                      <option value="oldest">오래된순</option>
                      <option value="views">조회순</option>
                    </select>
                  </div>
                  <NoticeSearch onSearch={handleNoticeSearch} />
                </div>
              </div>
              <NoticeTable notices={noticePaginated} onRowClick={handleNoticeRowClick} />
              <NoticePagination currentPage={noticeCurrentPage} totalPages={noticeTotalPages} onPageChange={handleNoticePageChange} />
            </div>
          </section>
        ) : (
          <section className="obituary">
            <div className="wrap">
              <h3 className="obituary-heading" data-heading="5xl">
                부고
              </h3>
              <div className="obituary-top">
                <p className="obituary-count">
                  총 <strong>{obituaryFiltered.length}</strong>건
                </p>
                <ObituarySearchForm
                  searchQuery={obituarySearch}
                  onSearchChange={setObituarySearch}
                  onSearch={handleObituarySearch}
                />
              </div>
              <ul className="obituary-list" data-grid="4">
                {obituaryPaginated.map((item) => (
                  <ObituaryCard
                    key={item.id}
                    id={item.id}
                    title={item.title}
                    description={item.description}
                    date={item.date}
                  />
                ))}
              </ul>
              <ObituaryPagination currentPage={obituaryCurrentPage} totalPages={obituaryTotalPages} onPageChange={handleObituaryPageChange} />
            </div>
          </section>
        )}
      </div>
    </div>
  );
}
