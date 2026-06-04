import { useEffect, useRef, useCallback, useState } from "react";
import "./css/Announcement.css";

const FALLBACK_NEWS = [
];

function getNewsImage(item, index) {
  const file = item?.thumbnail || item?.thumbnail_url || item?.image || item?.image_url || item?.file_path || item?.file_url;
  if (!file) return FALLBACK_NEWS[index % FALLBACK_NEWS.length].image;
  if (String(file).startsWith("http")) return file;
  if (String(file).startsWith("/")) return file;
  return `/uploads/announcement/${file}`;
}

export default function Announcement({ items = [], section = null }) {
  const withImage = items.filter((item) => item?.image).slice(0, 20);
  const cards = withImage.length
    ? withImage.map((item, idx) => ({
        title: item.title || "새로운 소식",
        image: getNewsImage(item, idx),
        link: item.link || "#",
        btnText: item.link_text || null,
      }))
    : FALLBACK_NEWS;

  const noticeText = items[0]?.title || "게시판에 고정된 공지사항이 들어갑니다.";

  const trackRef = useRef(null);
  const sectionRef = useRef(null);
  const stateRef = useRef({ current: 0, total: cards.length, animating: false, timer: null, dragging: false, hasDragged: false, startX: 0, startSX: 0 });
  const [selectedCard, setSelectedCard] = useState(null);
  const [urgentItem, setUrgentItem] = useState(null);
  const [dotIndex, setDotIndex] = useState(0);
  const setDotIndexRef = useRef(setDotIndex);
  // Show urgent popup once per session for the first urgent item
  useEffect(() => {
    const first = items.find((item) => item?.emergency_level === "urgent");
    if (!first) return;
    const key = `urgent-dismissed-${first.id || first.title}`;
    if (sessionStorage.getItem(key)) return;
    const expires = Number(localStorage.getItem(key) || 0);
    if (expires && Date.now() < expires) return;
    setUrgentItem(first);
  }, [items]);

  const dismissUrgent = useCallback((skipToday) => {
    if (!urgentItem) return;
    const key = `urgent-dismissed-${urgentItem.id || urgentItem.title}`;
    if (skipToday) {
      const expires = Date.now() + 24 * 60 * 60 * 1000;
      localStorage.setItem(key, String(expires));
    }
    sessionStorage.setItem(key, "1");
    setUrgentItem(null);
  }, [urgentItem]);

  // Keep a stable ref so the DOM event listener can access current values
  const cardsRef = useRef(cards);
  const setSelectedCardRef = useRef(setSelectedCard);
  useEffect(() => { cardsRef.current = cards; }, [cards]);

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
    console.log(`goTo ${idx}, current: ${s.current}, total: ${s.total}`);
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

    if (total === 0) return;

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
      setDotIndexRef.current(s.current - total);
    };

    const onResize = () => moveTo(s.current, false);

    const prevBtn = section.querySelector(".slider-nav__btn.is-prev");
    const nextBtn = section.querySelector(".slider-nav__btn.is-next");
    const mobilePrevBtn = section.querySelector(".main-news__mobile-btn--prev");
    const mobileNextBtn = section.querySelector(".main-news__mobile-btn--next");
    const slider  = section.querySelector(".main-news__slider");

    const onPrev = () => { goTo(s.current - 1); startAuto(); };
    const onNext = () => { goTo(s.current + 1); startAuto(); };

    track.addEventListener("transitionend", onTransitionEnd);
    window.addEventListener("resize", onResize);
    if (prevBtn) prevBtn.addEventListener("click", onPrev);
    if (nextBtn) nextBtn.addEventListener("click", onNext);
    if (mobilePrevBtn) mobilePrevBtn.addEventListener("click", onPrev);
    if (mobileNextBtn) mobileNextBtn.addEventListener("click", onNext);
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
    const onClickCapture = (e) => {
      if (s.hasDragged) { e.preventDefault(); e.stopPropagation(); s.hasDragged = false; return; }
      // Event delegation: open popup for whichever card (original or clone) was clicked
      const cardEl = e.target.closest(".news-card");
      if (!cardEl) return;
      const idx = parseInt(cardEl.dataset.cardIdx, 10);
      if (!isNaN(idx)) {
        const card = cardsRef.current[idx];
        setSelectedCardRef.current(card);
        if (typeof window.gtag === "function") {
          window.gtag("event", "announcement_card_click", {
            event_category: "Announcement",
            event_label: card?.title || "unknown",
          });
        }
      }
    };
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
      if (mobilePrevBtn) mobilePrevBtn.removeEventListener("click", onPrev);
      if (mobileNextBtn) mobileNextBtn.removeEventListener("click", onNext);
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
  }, [items.length]);

  // Lock body scroll and handle Escape key when modal is open
  useEffect(() => {
    if (!selectedCard && !urgentItem) return;
    document.body.style.overflow = "hidden";
    const onKey = (e) => {
      if (e.key === "Escape") {
        if (urgentItem) dismissUrgent(false);
        else setSelectedCard(null);
      }
    };
    document.addEventListener("keydown", onKey);
    return () => {
      document.body.style.overflow = "";
      document.removeEventListener("keydown", onKey);
    };
  }, [selectedCard, urgentItem, dismissUrgent]);

  return (
    <>
    <div className="main-bottom-bg">
      <div className="main-bottom-bg__texture" aria-hidden="true">
        <img src="/images/main/news-bg-texture.jpg" alt="" />
      </div>

      <section className="main-news" ref={sectionRef}>
        <div className="wrap">
          <div className="main-news__head" data-ani="top">
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

          <div className="main-news__notice tablet-block" data-ani="top">
            <div className="main-news__notice-label">
              <img src="/images/main/icon-notice.svg" alt="" aria-hidden="true" />
              <span data-text="sm-sb">공지사항</span>
            </div>
            <a data-text="sm" className="main-news__notice-text" href="/news/notice">{noticeText}</a>
          </div>

          <div className="main-news__slider-wrap">
            <div className="main-news__slider" data-ani="top">
              <div className="main-news__track" id="newsTrack" ref={trackRef}>
                {cards.map((card, idx) => (
                  <article className="news-card" key={`${card.title}-${idx}`} data-card-idx={idx} style={{ cursor: "pointer" }}>
                    <div className="news-card__thumb"><img src={card.image} alt={card.title} /></div>
                    <div className="news-card__body">
                      <h3 data-heading="xl">{card.title}</h3>
                      {card.btnText && <a className="btn-basic btn-basic--white" href={card.link} target="_blank" rel="noopener noreferrer">{card.btnText}</a>}
                    </div>
                  </article>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    {/* 긴급 공지 팝업 */}
    {urgentItem && (
      <div className={`urgent-popup${urgentItem ? " is-open" : ""}`} role="alertdialog" aria-modal="true" aria-label="긴급 공지">
        <div className="urgent-popup__overlay" onClick={() => dismissUrgent(false)} />
        <div className="urgent-popup__content">
          <div className="urgent-popup__badge">
            <span className="urgent-popup__badge-icon" aria-hidden="true">🚨</span>
            <span className="urgent-popup__badge-text">긴급 공지</span>
          </div>
          {(urgentItem.thumbnail || urgentItem.thumbnail_url || urgentItem.image || urgentItem.image_url) && (
            <img
              className="urgent-popup__image"
              src={getNewsImage(urgentItem, 0)}
              alt={urgentItem.title || "긴급 공지"}
            />
          )}
          <div className="urgent-popup__body">
            <p className="urgent-popup__title">{urgentItem.title}</p>
            {urgentItem.link && urgentItem.link !== "#" && (
              <a
                className="urgent-popup__link"
                href={urgentItem.link}
                target="_blank"
                rel="noopener noreferrer"
                onClick={() => dismissUrgent(false)}
              >
                {urgentItem.link_text || "자세히 보기"}
              </a>
            )}
          </div>
          <div className="urgent-popup__footer">
            <button className="urgent-popup__skip" type="button" onClick={() => dismissUrgent(true)}>
              오늘 하루 보지 않기
            </button>
            <button className="urgent-popup__close" type="button" aria-label="닫기" onClick={() => dismissUrgent(false)}>
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L13 13M13 1L1 13" stroke="#333" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    )}

    {/* 뉴스카드 팝업 */}
    {selectedCard && (
      <div className="news-card-modal is-open" role="dialog" aria-modal="true" aria-label="뉴스카드 크게 보기">
        <div className="news-card-modal__overlay" onClick={() => setSelectedCard(null)} />
        <div className="news-card-modal__content">
          <button className="news-card-modal__close" type="button" aria-label="닫기" onClick={() => setSelectedCard(null)}>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round"/>
            </svg>
          </button>
          <div className="news-card__inr">
            <img className="news-card-modal__image" src={selectedCard.image} alt={selectedCard.title} />
            <div className="news-card-modal__body">
              <h3 className="news-card-modal__title">{selectedCard.title}</h3>
              {selectedCard.btnText && (
                <a
                  className="news-card-modal__button"
                  href={selectedCard.link}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => {
                    if (typeof window.gtag === "function") {
                      window.gtag("event", "announcement_card_link_click", {
                        event_category: "Announcement",
                        event_label: selectedCard.title || "unknown",
                        link_url: selectedCard.link || "",
                      });
                    }
                  }}
                >{selectedCard.btnText}</a>
              )}
            </div>
          </div>
        </div>
      </div>
    )}
  </>
  );
}
