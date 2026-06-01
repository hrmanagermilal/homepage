import { useEffect, useMemo, useRef, useState } from "react";
import { api } from "../api/client";
import "./css/SubPage.css";
import "./css/BulletinViewPage.css";
import "./landing_components/css/Jubo.css";
import BulletinSubVisual from "./bulletin_components/BulletinSubVisual";

function getBulletinIdFromPath() {
  const match = window.location.pathname.match(/\/(\d+)/);
  return match ? Number(match[1]) : null;
}

function BulletinImageViewer({ bulletin }) {
  const pages = useMemo(() => {
    if (!bulletin?.images?.length) return [];
    return bulletin.images
      .slice()
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
  }, [bulletin]);

  const [activeIndex, setActiveIndex] = useState(0);
  const [popupOpen, setPopupOpen] = useState(false);
  const thumbsRef = useRef(null);

  const currentImg = pages[activeIndex];

  const goTo = (idx) => {
    const clamped = Math.max(0, Math.min(pages.length - 1, idx));
    setActiveIndex(clamped);
    // Scroll active thumbnail into view
    const list = thumbsRef.current;
    if (list) {
      const item = list.children[clamped];
      if (item) item.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }
  };

  if (!pages.length) {
    return <p className="bulletin-view__no-images">등록된 이미지가 없습니다.</p>;
  }

  const checkImageUrl = (url) => {
    // if url's first character is not /, prepend /
    if (url && !url.startsWith("/")) {
      return "/" + url;
    }
    return url;
  }

  return (
    <>
      <div className="bulletin-viewer">
        <div className="bulletin-viewer__main-wrap">
          {/* Big image */}
          <div
            className="main-weekly__thumb-wrap bulletin-viewer__main"
            role="button"
            tabIndex={0}
            onClick={() => setPopupOpen(true)}
            onKeyDown={(e) => e.key === "Enter" && setPopupOpen(true)}
            aria-label="크게 보기"
          >
            <div className="bulletin-viewer__card">
              <img key={activeIndex} src={checkImageUrl(currentImg.image_url)} alt={`${bulletin.title} ${activeIndex + 1}페이지`} />
            </div>
            <div className="main-weekly__hover-btn">
              <img src="/images/main/icon-zoom.svg" alt="" />
              <p>크게 보기</p>
            </div>
          </div>

          {/* Thumbnail column with up/down arrows */}
          <div className="bulletin-viewer__thumb-col">

            <ul className="bulletin-viewer__thumbs" ref={thumbsRef}>
              {pages.map((img, idx) => (
                <li
                  key={img.id ?? idx}
                  className={`main-weekly__nav-item${idx === activeIndex ? " is-active" : ""}`}
                  onClick={() => goTo(idx)}
                  role="button"
                  tabIndex={0}
                  onKeyDown={(e) => e.key === "Enter" && goTo(idx)}
                  aria-label={`${idx + 1}페이지`}
                >
                  <img src={checkImageUrl(img.image_url)} alt={`주보 ${idx + 1}페이지`} />
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>

      {/* Popup */}
      <div
        className={`weekly-popup${popupOpen ? " is-open" : ""}`}
        role="dialog"
        aria-modal="true"
        aria-label="주보 크게 보기"
        onClick={() => setPopupOpen(false)}
      >
        <button
          className="weekly-popup__close"
          type="button"
          aria-label="닫기"
          onClick={(e) => { e.stopPropagation(); setPopupOpen(false); }}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        <img
          className="weekly-popup__img"
          src={checkImageUrl(currentImg?.image_url)}
          alt={`${bulletin.title} ${activeIndex + 1}페이지`}
          onClick={(e) => e.stopPropagation()}
        />
      </div>
    </>
  );
}

export default function BulletinViewPage() {
  const [bulletin, setBulletin] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [currentId, setCurrentId] = useState(() => getBulletinIdFromPath());

  useEffect(() => {
    const syncId = () => setCurrentId(getBulletinIdFromPath());
    window.addEventListener("popstate", syncId);
    window.addEventListener("locationchange", syncId);
    return () => {
      window.removeEventListener("popstate", syncId);
      window.removeEventListener("locationchange", syncId);
    };
  }, []);

  useEffect(() => {
    if (!currentId) return;
    setLoading(true);
    setError("");
    api
      .getBulletinById(currentId)
      .then((res) => { setBulletin(res?.data ?? null); })
      .catch(() => setError("주보를 불러오는 데 실패했습니다."))
      .finally(() => setLoading(false));
  }, [currentId]);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [bulletin]);

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#bulletin");
    window.dispatchEvent(new Event("locationchange"));
  };

  return (
    <>
      <BulletinSubVisual />
      <div className="sub-content" id="content">
        <section className="bulletin board-view">
          <div className="wrap-narrow">
            {loading ? (
              <div className="bulletin-view__loading">주보를 불러오는 중입니다...</div>
            ) : error || !bulletin ? (
              <div className="bulletin-view__error">{error || "주보를 찾을 수 없습니다."}</div>
            ) : (
              <>
                <div className="bulletin-view__header">
                  <h3 className="bulletin-view__title">{bulletin.title}</h3>
                  <p className="bulletin-view__meta">
                    {bulletin.year && bulletin.week_number && (
                      <span className="bulletin-view__meta-item">{bulletin.year}년 {bulletin.week_number}주</span>
                    )}
                    {bulletin.created_at && (
                      <span className="bulletin-view__meta-item">{String(bulletin.created_at).slice(0, 10)}</span>
                    )}
                  </p>
                </div>
                <BulletinImageViewer bulletin={bulletin} />
              </>
            )}
            <div className="view-btn-wrap">
              <button type="button" className="btn-list" onClick={handleListClick}>
                목록
              </button>
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
