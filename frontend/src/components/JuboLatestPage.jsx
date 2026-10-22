import { useEffect, useMemo, useState } from "react";
import { api } from "../api/client";
import "./landing_components/css/Jubo.css";
import "./css/JuboLatestPage.css";

const normalizeUrl = (url) => (url && !/^(\/|https?:)/.test(url) ? `/${url}` : url);

export default function JuboLatestPage() {
  const [bulletin, setBulletin] = useState(null);
  const [status, setStatus] = useState("loading");
  const [zoomSrc, setZoomSrc] = useState(null);

  useEffect(() => {
    let mounted = true;
    document.title = "밀알 주보";
    api
      .getLastBulletin()
      .then((res) => {
        if (!mounted) return;
        const data = res?.data ?? null;
        setBulletin(data);
        setStatus(data?.images?.length ? "ready" : "empty");
      })
      .catch(() => mounted && setStatus("error"));
    return () => {
      mounted = false;
    };
  }, []);

  const pages = useMemo(
    () => (bulletin?.images ?? []).slice().sort((a, b) => (a.order ?? 0) - (b.order ?? 0)),
    [bulletin]
  );

  return (
    <main className="jubo-latest">
      <header className="jubo-latest__head">
        <div className="jubo-latest__title">
          <h1>밀알 주보</h1>
          {bulletin?.title && <p>{bulletin.title}</p>}
        </div>
        <a className="jubo-latest__logo" href="/" aria-label="밀알교회 홈페이지로 이동">
          <img src="/images/common/logo.png" alt="밀알교회" />
        </a>
      </header>

      {status === "loading" && <p className="jubo-latest__msg">불러오는 중...</p>}
      {status === "empty" && <p className="jubo-latest__msg">등록된 주보가 없습니다.</p>}
      {status === "error" && <p className="jubo-latest__msg">주보를 불러오지 못했습니다.</p>}

      <ul className="jubo-latest__list">
        {pages.map((img, idx) => (
          <li key={img.id ?? idx}>
            <img
              src={normalizeUrl(img.image_url)}
              alt={`${bulletin.title || "주보"} ${idx + 1}페이지`}
              loading={idx === 0 ? "eager" : "lazy"}
              onClick={() => setZoomSrc(normalizeUrl(img.image_url))}
            />
          </li>
        ))}
      </ul>

      <div className={`weekly-popup${zoomSrc ? " is-open" : ""}`} role="dialog" aria-modal="true" onClick={() => setZoomSrc(null)}>
        <button className="weekly-popup__close" type="button" aria-label="닫기" onClick={() => setZoomSrc(null)}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        {zoomSrc && <img className="weekly-popup__img" src={zoomSrc} alt="주보 크게 보기" onClick={(e) => e.stopPropagation()} />}
      </div>
    </main>
  );
}
