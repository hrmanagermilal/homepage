import { useMemo, useState } from "react";
import "./css/Jubo.css";

const FALLBACK_IMAGES = [
  "/images/main/weekly-bulletin-01.png",
  "/images/main/weekly-bulletin-02.png",
  "/images/main/weekly-bulletin-03.png",
  "/images/main/weekly-bulletin-04.png",
  "/images/main/weekly-bulletin-05.png",
  "/images/main/weekly-bulletin-05.png",
];

function getBulletinImage(item, index) {
  const file = item?.thumbnail || item?.thumbnail_url || item?.image || item?.image_url || item?.file_path || item?.file_url;
  if (!file) return FALLBACK_IMAGES[index % FALLBACK_IMAGES.length];
  if (String(file).startsWith("http")) return file;
  if (String(file).startsWith("/")) return file;
  return `/uploads/bulletin/${file}`;
}

export default function Jubo({ items = [], section = null }) {
  const bulletins = useMemo(() => {
    if (items.length) return items.slice(0, 6);
    return FALLBACK_IMAGES.map((img, index) => ({ id: `fallback-${index}`, title: `주보 ${index + 1}`, _img: img }));
  }, [items]);

  const [activeIndex, setActiveIndex] = useState(0);
  const [popupOpen, setPopupOpen] = useState(false);

  const currentItem = bulletins[activeIndex];
  const currentImage = currentItem?._img || getBulletinImage(currentItem, activeIndex);

  const goPrev = () => setActiveIndex((prev) => (prev - 1 + bulletins.length) % bulletins.length);
  const goNext = () => setActiveIndex((prev) => (prev + 1) % bulletins.length);

  return (
    <>
      <section id="weekly" className="main-weekly">
        <div className="main-weekly__inr">
          <div className="main-weekly__bg">
            <img src="/images/main/weekly-bg.jpg" alt="" />
          </div>

          <div className="wrap-narrow">
            <div className="main-weekly__layout">
              <div className="main-weekly__thumb-area">
                <div className="main-weekly__thumb-wrap" role="button" tabIndex={0} onClick={() => setPopupOpen(true)} onKeyDown={(e) => (e.key === "Enter" ? setPopupOpen(true) : null)}>
                  <div className="main-weekly__card">
                    <img src={currentImage} alt={currentItem?.title || "밀알 주보"} />
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
                  <a className="btn-basic-big btn-basic-big--trans" href="/news/notice">전체 주보 보러가기</a>
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
                  {bulletins.map((item, idx) => (
                    <li key={item.id || idx} className={`main-weekly__nav-item${idx === activeIndex ? " is-active" : ""}`} onClick={() => setActiveIndex(idx)}>
                      <img src={item._img || getBulletinImage(item, idx)} alt={item.title || `주보 ${idx + 1}`} />
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div className={`weekly-popup${popupOpen ? " is-open" : ""}`} role="dialog" aria-modal="true" aria-label="주보 크게 보기">
        <button className="weekly-popup__close" type="button" aria-label="닫기" onClick={() => setPopupOpen(false)}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        <img className="weekly-popup__img" src={currentImage} alt={currentItem?.title || "주보 크게 보기"} />
      </div>
    </>
  );
}
