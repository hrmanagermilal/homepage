import { useEffect, useMemo, useState } from "react";
import "./css/Hero.css";

const SLIDES = [
  { src: "/images/main/main-visual-slide-01.jpg", alt: "" },
  { src: "/images/main/main-visual-slide-02.jpg", alt: "" },
  { src: "/images/main/main-visual-slide-03.png", alt: "" },
];

const DEFAULT_QUICK_LINKS = [
  {
    href: "#worship",
    icon: "/images/main/icon-quick-worship.svg",
    title: "예배 시간 안내",
    desc: "밀알교회의 예배시간을 알려드립니다.",
  },
  {
    href: "#weekly",
    icon: "/images/main/icon-quick-bulletin.svg",
    title: "주보",
    desc: "예배와 소식 내용을 확인해 보세요",
  },
];

function resolveMediaPath(raw, defaultBase) {
  if (!raw) return null;
  const value = String(raw).trim();
  if (!value) return null;

  if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("data:")) {
    return value;
  }

  if (value.startsWith("/")) {
    return value;
  }

  if (value.startsWith("uploads/")) {
    return `/${value}`;
  }

  return `${defaultBase}${value}`;
}

export default function Hero({ hero = null, quickLinks = [] }) {
  const [frontImageFailed, setFrontImageFailed] = useState(false);

  const slides = useMemo(() => {
    const source = Array.isArray(hero?.backgroundImages)
      ? hero.backgroundImages
      : Array.isArray(hero?.background_images)
        ? hero.background_images
        : [];

    const mapped = source
      .map((item) => {
        if (!item) return null;
        if (typeof item === "string") {
          return {
            src: resolveMediaPath(item, "/uploads/hero/background/"),
            alt: "",
          };
        }
        const raw = item.imageUrl || item.image_url || item.url || item.path || item.file_name;
        if (!raw) return null;
        return {
          src: resolveMediaPath(raw, "/uploads/hero/background/"),
          alt: item.alt || "",
        };
      })
      .filter((item) => Boolean(item?.src));

    return mapped.length ? mapped : SLIDES;
  }, [hero]);

  const frontImageSrc = useMemo(() => {
    const raw =
      hero?.frontImage ||
      hero?.front_image ||
      hero?.frontImageUrl ||
      hero?.front_image_url ||
      hero?.textImage ||
      hero?.text_image;

    if (!raw) return "/images/main/main-visual-text.png";
    if (typeof raw !== "string") {
      const objRaw = raw?.imageUrl || raw?.image_url || raw?.url || raw?.path || raw?.file_name;
      if (!objRaw) return "/images/main/main-visual-text.png";
      return resolveMediaPath(objRaw, "/uploads/hero/front/") || "/images/main/main-visual-text.png";
    }
    return resolveMediaPath(raw, "/uploads/hero/front/") || "/images/main/main-visual-text.png";
  }, [hero]);

  useEffect(() => {
    setFrontImageFailed(false);
  }, [frontImageSrc]);

  const [currentSlide, setCurrentSlide] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slides.length);
    }, 7000);
    return () => clearInterval(timer);
  }, [slides.length]);

  useEffect(() => {
    if (currentSlide >= slides.length) {
      setCurrentSlide(0);
    }
  }, [currentSlide, slides.length]);

  const goTo = (idx) => setCurrentSlide(idx);
  const goPrev = () => setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length);
  const goNext = () => setCurrentSlide((prev) => (prev + 1) % slides.length);

  const displayLinks = quickLinks.length > 0
    ? quickLinks.slice(0, 2).map((ql) => ({
        href: ql.link || "#",
        icon: ql.iconUrl ? (resolveMediaPath(ql.iconUrl, "/uploads/hero/") || DEFAULT_QUICK_LINKS[0].icon) : DEFAULT_QUICK_LINKS[0].icon,
        title: ql.title || "",
        desc: ql.description || "",
      }))
    : DEFAULT_QUICK_LINKS;

  return (
    <section className="main-visual" id="hero" aria-label="메인 비주얼">

      {slides.map((slide, idx) => (
        <div
          key={idx}
          className={`main-visual__slide${currentSlide === idx ? " is-active" : ""}`}
          aria-hidden="true"
        >
          <img src={slide.src} alt={slide.alt} />
        </div>
      ))}

      <div className="main-visual__overlay" aria-hidden="true"></div>
      <div className="main-visual__gradient" aria-hidden="true"></div>

      <div className="main-visual__ellipse" aria-hidden="true">
        <img src="/images/main/main-visual-ellipse.svg" alt="" />
      </div>

      <div className="main-visual__cont">
        <h1 className="main-visual__text-img">
          <img
            src={frontImageFailed ? "/images/main/main-visual-text.png" : frontImageSrc}
            alt="사람을 세우다"
            onError={() => setFrontImageFailed(true)}
          />
        </h1>
        <p className="main-visual__sub">
          밀알교회는 하나님의 사람을 세웁니다.<br />
          모퉁이돌 되신 예수 안에 함께 지어져 가는 공동체입니다.
        </p>
      </div>

      <div className="main-visual__nav" role="group" aria-label="슬라이드 네비게이션">
        <button className="main-visual__nav-btn is-prev" type="button" aria-label="이전 슬라이드" onClick={goPrev}>
          <img src="/images/main/icon-arrow-visual.svg" alt="" />
        </button>
        <div className="main-visual__dots">
          {slides.map((_, idx) => (
            <button
              key={idx}
              className={`main-visual__dot${currentSlide === idx ? " is-active" : ""}`}
              type="button"
              aria-label={`슬라이드 ${idx + 1}`}
              onClick={() => goTo(idx)}
            />
          ))}
        </div>
        <button className="main-visual__nav-btn is-next" type="button" aria-label="다음 슬라이드" onClick={goNext}>
          <img src="/images/main/icon-arrow-visual.svg" alt="" />
        </button>
      </div>

      <div className="main-visual__scroll-down" aria-hidden="true">
        <i></i>
        <span>SCROLL DOWN</span>
      </div>

      <div className="main-visual__quick">
        <ul className="main-visual__quick-inner">
          {displayLinks.map((link, idx) => (
            <li key={idx}>
              <a className="main-visual__quick-item" href={link.href}>
                <div className="main-visual__quick-icon">
                  <img src={link.icon} alt="" aria-hidden="true" />
                </div>
                <div className="main-visual__quick-body">
                  <strong>{link.title}</strong>
                  <span>{link.desc}</span>
                </div>
                <div className="main-visual__quick-arrow" aria-hidden="true">
                  <img src="/images/main/icon-arrow-quick.svg" alt="" />
                </div>
              </a>
            </li>
          ))}
        </ul>
      </div>

    </section>
  );
}
