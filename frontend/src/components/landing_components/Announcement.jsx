import { useEffect, useRef, useCallback } from "react";
import "../css/Announcement.css";

const FALLBACK_NEWS = [
  { title: "제3회 가스펠오락관 - 암송축제편", image: "/images/main/news-thumb-01.jpg", link: "#", btnText: "신청하러 가기" },
  { title: "BAPTISM", image: "/images/main/news-thumb-02.jpg", link: "#", btnText: null },
  { title: "워크톤 페스티벌", image: "/images/main/news-thumb-03.jpg", link: "#", btnText: "신청하러 가기" },
  { title: "새로운 소식", image: "/images/main/news-thumb-04.jpg", link: "#", btnText: "신청하러 가기" },
  { title: "새로운 소식", image: "/images/main/news-thumb-05.jpg", link: "#", btnText: "신청하러 가기" },
];

function getNewsImage(item, index) {
  const file = item?.thumbnail || item?.thumbnail_url || item?.image || item?.image_url || item?.file_path || item?.file_url;
  if (!file) return FALLBACK_NEWS[index % FALLBACK_NEWS.length].image;
  if (String(file).startsWith("http")) return file;
  if (String(file).startsWith("/")) return file;
  return `/uploads/announcement/${file}`;
}

export default function Announcement({ items = [], section = null }) {
  const cards = items.length
    ? items.slice(0, 5).map((item, idx) => ({
        title: item.title || "새로운 소식",
        image: getNewsImage(item, idx),
        link: item.link || "#",
        btnText: item.link ? "자세히 보기" : null,
      }))
    : FALLBACK_NEWS;

  const noticeText = items[0]?.title || "게시판에 고정된 공지사항이 들어갑니다.";

  const trackRef = useRef(null);
  const sectionRef = useRef(null);
  const stateRef = useRef({ current: 0, total: cards.length, animating: false, timer: null, dragging: false, hasDragged: false, startX: 0, startSX: 0 });

  const getStep = useCallback(() => {
    const track = trackRef.current;
    if (!track) return 0;
    const card = track.querySelector(".news-card");
    if (!card) return 0;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;
    return card.offsetWidth + gap;
  }, []);

  const moveTo = useCallback((idx, animate) => {
    const track = trackRef.current;
    if (!track) return;
    if (!animate) {
      track.style.transition = "none";
      track.offsetHeight; // reflow
    } else {
      track.style.transition = "";
    }
    track.style.transform = `translateX(-${idx * getStep()}px)`;
  }, [getStep]);

  const goTo = useCallback((idx) => {
    const s = stateRef.current;
    if (s.animating) return;
    s.animating = true;
    s.current = idx;
    moveTo(idx, true);
  }, [moveTo]);

  const startAuto = useCallback(() => {
    const s = stateRef.current;
    if (s.timer) clearInterval(s.timer);
    s.timer = setInterval(() => goTo(s.current + 1), 3000);
  }, [goTo]);

  const stopAuto = useCallback(() => {
    const s = stateRef.current;
    if (s.timer) { clearInterval(s.timer); s.timer = null; }
  }, []);

  useEffect(() => {
    const track = trackRef.current;
    const section = sectionRef.current;
    if (!track || !section) return;

    const s = stateRef.current;
    const total = cards.length;
    s.total = total;
    s.current = total;

    // Insert clones: [clones] [originals] [clones]
    const origCards = Array.from(track.querySelectorAll(".news-card"));
    const preFrag = document.createDocumentFragment();
    origCards.forEach((c) => preFrag.appendChild(c.cloneNode(true)));
    track.insertBefore(preFrag, track.firstChild);
    origCards.forEach((c) => track.appendChild(c.cloneNode(true)));

    moveTo(s.current, false);
    startAuto();

    const onTransitionEnd = (e) => {
      if (e.propertyName !== "transform") return;
      if (s.current < total) { s.current += total; moveTo(s.current, false); }
      else if (s.current >= total * 2) { s.current -= total; moveTo(s.current, false); }
      s.animating = false;
    };

    const onResize = () => moveTo(s.current, false);

    const prevBtn = section.querySelector(".slider-nav__btn.is-prev");
    const nextBtn = section.querySelector(".slider-nav__btn.is-next");
    const slider  = section.querySelector(".main-news__slider");

    const onPrev = () => { goTo(s.current - 1); startAuto(); };
    const onNext = () => { goTo(s.current + 1); startAuto(); };

    track.addEventListener("transitionend", onTransitionEnd);
    window.addEventListener("resize", onResize);
    if (prevBtn) prevBtn.addEventListener("click", onPrev);
    if (nextBtn) nextBtn.addEventListener("click", onNext);
    if (slider) { slider.addEventListener("mouseenter", stopAuto); slider.addEventListener("mouseleave", startAuto); }

    // Drag / swipe
    const swipeStart = (x) => {
      if (s.animating) return;
      s.dragging = true; s.hasDragged = false;
      s.startX = x; s.startSX = s.current * getStep();
      track.style.transition = "none";
      track.classList.add("is-dragging");
      stopAuto();
    };
    const swipeMove = (x) => {
      if (!s.dragging) return;
      const delta = s.startX - x;
      if (Math.abs(delta) > 5) s.hasDragged = true;
      track.style.transform = `translateX(-${s.startSX + delta}px)`;
    };
    const swipeEnd = (x) => {
      if (!s.dragging) return;
      s.dragging = false;
      track.classList.remove("is-dragging");
      const delta = s.startX - x;
      if (Math.abs(delta) > 50) { goTo(delta > 0 ? s.current + 1 : s.current - 1); }
      else { moveTo(s.current, true); s.animating = false; }
      startAuto();
    };

    const onMouseDown = (e) => swipeStart(e.clientX);
    const onMouseMove = (e) => swipeMove(e.clientX);
    const onMouseUp   = (e) => swipeEnd(e.clientX);
    const onTouchStart = (e) => swipeStart(e.touches[0].clientX);
    const onTouchMove  = (e) => swipeMove(e.touches[0].clientX);
    const onTouchEnd   = (e) => swipeEnd(e.changedTouches[0].clientX);
    const onClickCapture = (e) => { if (s.hasDragged) { e.preventDefault(); s.hasDragged = false; } };
    const onDragStart = (e) => e.preventDefault();

    track.addEventListener("mousedown",   onMouseDown);
    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup",   onMouseUp);
    track.addEventListener("touchstart",  onTouchStart, { passive: true });
    track.addEventListener("touchmove",   onTouchMove,  { passive: true });
    track.addEventListener("touchend",    onTouchEnd);
    track.addEventListener("click",       onClickCapture, true);
    track.addEventListener("dragstart",   onDragStart);

    return () => {
      stopAuto();
      track.removeEventListener("transitionend", onTransitionEnd);
      window.removeEventListener("resize", onResize);
      if (prevBtn) prevBtn.removeEventListener("click", onPrev);
      if (nextBtn) nextBtn.removeEventListener("click", onNext);
      if (slider) { slider.removeEventListener("mouseenter", stopAuto); slider.removeEventListener("mouseleave", startAuto); }
      track.removeEventListener("mousedown",   onMouseDown);
      document.removeEventListener("mousemove", onMouseMove);
      document.removeEventListener("mouseup",   onMouseUp);
      track.removeEventListener("touchstart",  onTouchStart);
      track.removeEventListener("touchmove",   onTouchMove);
      track.removeEventListener("touchend",    onTouchEnd);
      track.removeEventListener("click",       onClickCapture, true);
      track.removeEventListener("dragstart",   onDragStart);
    };
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div className="main-bottom-bg">
      <div className="main-bottom-bg__texture" aria-hidden="true">
        <img src="/images/main/news-bg-texture.jpg" alt="" />
      </div>

      <section className="main-news" ref={sectionRef}>
        <div className="wrap">
          <div className="main-news__head">
            <div className="main-title">
              <h2 data-heading="5xl" className="main-title__heading">새로운 소식</h2>
              <p className="main-title__sub">다양한 행사들과 소식을 놓치지 마세요.</p>
            </div>
            <div className="main-news__ctrl">
              <div className="main-news__notice tablet-none">
                <div className="main-news__notice-label">
                  <img src="/images/main/icon-notice.svg" alt="" aria-hidden="true" />
                  <span data-text="sm-sb">공지사항</span>
                </div>
                <a data-text="sm" className="main-news__notice-text" href="/news/notice">{noticeText}</a>
              </div>
              <div className="slider-nav slider-nav--white">
                <button className="slider-nav__btn is-prev" type="button" aria-label="이전">
                  <img src="/images/main/icon-arrow-news.svg" alt="" aria-hidden="true" />
                </button>
                <button className="slider-nav__btn is-next" type="button" aria-label="다음">
                  <img src="/images/main/icon-arrow-news.svg" alt="" aria-hidden="true" />
                </button>
              </div>
            </div>
          </div>

          <div className="main-news__notice tablet-block">
            <div className="main-news__notice-label">
              <img src="/images/main/icon-notice.svg" alt="" aria-hidden="true" />
              <span data-text="sm-sb">공지사항</span>
            </div>
            <a data-text="sm" className="main-news__notice-text" href="/news/notice">{noticeText}</a>
          </div>

          <div className="main-news__slider">
            <div className="main-news__track" id="newsTrack" ref={trackRef}>
              {cards.map((card, idx) => (
                <article className="news-card" key={`${card.title}-${idx}`}>
                  <div className="news-card__thumb"><img src={card.image} alt={card.title} /></div>
                  <div className="news-card__body">
                    <h3 data-heading="xl">{card.title}</h3>
                    {card.btnText && <a className="btn-basic btn-basic--white" href={card.link}>{card.btnText}</a>}
                  </div>
                </article>
              ))}
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}

