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
import BulletinSubVisual from "./bulletin_components/BulletinSubVisual";
import BulletinTable from "./bulletin_components/BulletinTable";
import BulletinPagination from "./bulletin_components/BulletinPagination";
import {api} from "../api/client";

const NEWS_LNB_ITEMS = [
  { label: "온라인 주보", key: "bulletin", href: "/news#bulletin" },
  { label: "공지", key: "notice", href: "/news#notice" },
  { label: "부고", key: "obituary", href: "/news#obituary" },
];

function getKeyFromHash(hash) {
  const key = (hash || "").replace("#", "");
  if (key === "obituary") return "obituary";
  if (key === "bulletin") return "bulletin";
  return "notice";
}

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

export default function JuboPage({ notices = [], obituaries = [], bulletins = [] }) {
  const containerRef = useRef(null);
  const [activeKey, setActiveKey] = useState(() => getKeyFromHash(window.location.hash));

  // Notice state
  const [noticeSearch, setNoticeSearch] = useState("");
  const [noticeSortOrder, setNoticeSortOrder] = useState("newest");
  const [noticeCurrentPage, setNoticeCurrentPage] = useState(1);
  const [noticeData, setNoticeData] = useState(notices);
  // Obituary state
  const [obituarySearch, setObituarySearch] = useState("");
  const [obituaryCurrentPage, setObituaryCurrentPage] = useState(1);
  const [obituaryData, setObituaryData] = useState(obituaries);

  // Bulletin state
  const [bulletinSearch, setBulletinSearch] = useState("");
  const [bulletinSortOrder, setBulletinSortOrder] = useState("newest");
  const [bulletinCurrentPage, setBulletinCurrentPage] = useState(1);
  const [bulletinData, setBulletinData] = useState(bulletins);

  useEffect(() => {
    const onHashChange = () => {
      setActiveKey(getKeyFromHash(window.location.hash));
    };
    window.addEventListener("hashchange", onHashChange);
    return () => window.removeEventListener("hashchange", onHashChange);
  }, []);

  useEffect(() => {
    if (bulletins.length <= 0) {
      api.getBulletins({ page: 1, limit: 50 }).then((response) => {
        const data = response?.data?.data ?? response?.data ?? [];
        console.log("Fetched bulletins:", data);
        setBulletinData(data);
      });
    }
  }, [bulletins]);

  useEffect(() => {
    console.log("Obituaries prop changed:", obituaries);
    if (obituaries.length <= 0) {
      api.getObituary({ page: 1, limit: 200 }).then((response) => {
        const data = response?.data?.data ?? response?.data ?? [];
        console.log("Fetched obituaries:", data);
        setObituaryData(data);
      });
    }
  }, [obituaries]);

  useEffect(() => {
    api.getNotices({ page: 1, limit: 500 }).then((response) => { 
      const data = response?.data?.data ?? response?.data ?? [];
      console.log("Fetched notices:", data);
      // Sort by created_at descending
      const sorted = data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      setNoticeData(sorted);
    });
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
    return noticeData.filter((item) =>
      item.title.toLowerCase().includes(noticeSearch.toLowerCase()) ||
      (item.author ?? "").toLowerCase().includes(noticeSearch.toLowerCase())
    );
  }, [noticeData, noticeSearch]);

  const noticeSorted = useMemo(() => {
    const data = [...noticeFiltered];
    if (noticeSortOrder === "newest") return data; // API returns newest-first
    if (noticeSortOrder === "oldest") return data.reverse();
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
    if (!obituarySearch) return obituaryData;
    return obituaryData.filter(
      (item) =>
        item.title.toLowerCase().includes(obituarySearch.toLowerCase()) ||
        (item.description ?? "").toLowerCase().includes(obituarySearch.toLowerCase())
    );
  }, [obituaryData, obituarySearch]);

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

  // Bulletin computed
  const bulletinFiltered = useMemo(() => {
    return bulletinData.filter((item) =>
      item.title.toLowerCase().includes(bulletinSearch.toLowerCase())
    );
  }, [bulletinData, bulletinSearch]);

  const bulletinSorted = useMemo(() => {
    const data = [...bulletinFiltered];
    if (bulletinSortOrder === "newest") return data;
    if (bulletinSortOrder === "oldest") return data.reverse();
    return data;
  }, [bulletinFiltered, bulletinSortOrder]);

  const bulletinTotalPages = Math.ceil(bulletinSorted.length / ITEMS_PER_PAGE);
  const bulletinPaginated = useMemo(() => {
    const startIdx = (bulletinCurrentPage - 1) * ITEMS_PER_PAGE;
    return bulletinSorted.slice(startIdx, startIdx + ITEMS_PER_PAGE);
  }, [bulletinSorted, bulletinCurrentPage]);

  const handleBulletinSearch = (query) => { setBulletinSearch(query); setBulletinCurrentPage(1); };
  const handleBulletinSortChange = (value) => { setBulletinSortOrder(value); setBulletinCurrentPage(1); };
  const handleBulletinPageChange = (page) => {
    setBulletinCurrentPage(page);
    document.getElementById("content")?.scrollIntoView({ behavior: "smooth", block: "start" });
  };
  const handleBulletinRowClick = (id) => {
    window.history.pushState({}, "", `/news/bulletin/${id}`);
    window.dispatchEvent(new Event("locationchange"));
  };

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        {activeKey === "bulletin" ? <BulletinSubVisual /> : activeKey === "obituary" ? <ObituarySubVisual /> : <NoticeSubVisual />}
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
        ) : activeKey === "bulletin" ? (
          <section className="notice">
            <div className="wrap-narrow">
              <div className="notice-top">
                <div className="notice-count">
                  총 <strong>{bulletinFiltered.length}</strong>개
                </div>
                <div className="notice-controls">
                </div>
              </div>
              <BulletinTable bulletins={bulletinPaginated} onRowClick={handleBulletinRowClick} />
              <BulletinPagination currentPage={bulletinCurrentPage} totalPages={bulletinTotalPages} onPageChange={handleBulletinPageChange} />
            </div>
          </section>
        ) : (
          <section className="obituary">
            <div className="wrap">
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
                    description={item.description?item.description:item.content}
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
