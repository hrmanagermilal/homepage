import { useMemo, useState } from "react";
import "./css/Jubo.css";

const FALLBACK_IMAGES = [
  "/images/main/weekly-bulletin-01.png",
  "/images/main/weekly-bulletin-02.png",
  "/images/main/weekly-bulletin-03.png",
  "/images/main/weekly-bulletin-04.png",
  "/images/main/weekly-bulletin-05.png",
  "/images/main/weekly-bulletin-06.png",
];

export default function Jubo({ items = [], section = null }) {
  // Use the most recent bulletin's images as individual pages
  const pages = useMemo(() => {
    const latest = items[0];
    if (latest?.images?.length) {
      return latest.images
        .slice()
        .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
        .map((img) => ({ id: img.id, src: img.image_url, title: latest.title }));
    }
    return FALLBACK_IMAGES.map((src, i) => ({ id: `fallback-${i}`, src, title: `주보 ${i + 1}` }));
  }, [items]);

  const [activeIndex, setActiveIndex] = useState(0);
  const [popupOpen, setPopupOpen] = useState(false);

  const currentPage = pages[activeIndex];
  const currentImage = currentPage?.src;

  const goPrev = () => setActiveIndex((prev) => (prev - 1 + pages.length) % pages.length);
  const goNext = () => setActiveIndex((prev) => (prev + 1) % pages.length);

  return (
    <>
      <section id="weekly" className="main-weekly" data-ani="top">
        <div className="main-weekly__inr">
          <div className="main-weekly__bg">
            <img src="/images/main/weekly-bg.jpg" alt="" />
          </div>

          <div className="wrap-narrow">
            <div className="main-weekly__layout">
              <div className="main-weekly__thumb-area">
                <div className="main-weekly__thumb-wrap" role="button" tabIndex={0} onClick={() => setPopupOpen(true)} onKeyDown={(e) => (e.key === "Enter" ? setPopupOpen(true) : null)}>
                  <div className="main-weekly__card">
                    <img key={activeIndex} src={currentImage} alt={currentPage?.title || "밀알 주보"} />
                  </div>
                  <div className="main-weekly__hover-btn">
                    <img src="/images/main/icon-zoom.svg" alt="" />
                    <p>크게 보기</p>
                  </div>
                </div>
              </div>

              <div className="main-weekly__cont">
                <div className="main-title">
                  <h2 data-heading="5xl" className="main-title__heading">밀알 주보</h2>
                  <p className="main-title__sub">예배와 교회 주요 소식들을 주보에서 확인하세요.</p>
                </div>

                <div className="main-weekly__btns">
                  <a className="btn-basic-big btn-basic-big--trans" href="/news#bulletin">전체 주보 보러가기</a>
                  <div className="slider-nav slider-nav--trans">
                    <button className="slider-nav__btn is-prev" type="button" aria-label="이전" onClick={goPrev}>
                      <img src="/images/main/icon-arrow-weekly.svg" alt="" aria-hidden="true" />
                    </button>
                    <button className="slider-nav__btn is-next" type="button" aria-label="다음" onClick={goNext}>
                      <img src="/images/main/icon-arrow-weekly.svg" alt="" aria-hidden="true" />
                    </button>
                  </div>
                </div>

                <ul className="main-weekly__nav" id="weeklyNav">
                  {pages.map((page, idx) => (
                    <li key={page.id} className={`main-weekly__nav-item${idx === activeIndex ? " is-active" : ""}`} onClick={() => setActiveIndex(idx)}>
                      <img src={page.src} alt={`주보 ${idx + 1}페이지`} />
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div className={`weekly-popup${popupOpen ? " is-open" : ""}`} role="dialog" aria-modal="true" aria-label="주보 크게 보기" onClick={() => setPopupOpen(false)}>
        <button className="weekly-popup__close" type="button" aria-label="닫기" onClick={() => setPopupOpen(false)}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        <img className="weekly-popup__img" src={currentImage} alt={currentPage?.title || "주보 크게 보기"} onClick={(e) => e.stopPropagation()} />
      </div>
    </>
  );
}
