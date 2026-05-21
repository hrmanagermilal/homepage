import { useEffect, useState } from "react";
import "./css/SubPage.css";
import "./css/ObituaryViewPage.css";
import ObituaryViewSubVisual from "./obituary_components/ObituaryViewSubVisual";
import ObituaryViewContent from "./obituary_components/ObituaryViewContent";
import ObituaryViewNavigation from "./obituary_components/ObituaryViewNavigation";

function getObituaryIdFromPath() {
  const match = window.location.pathname.match(/\/news\/obituary\/(\d+)/);
  return match ? Number(match[1]) : null;
}

function getObituaryIndexById(obituaries, id) {
  if (!id || !obituaries.length) return 0;
  const index = obituaries.findIndex((item) => item.id === id);
  return index !== -1 ? index : 0;
}

export default function ObituaryViewPage({ obituaries = [] }) {
  const [currentIndex, setCurrentIndex] = useState(() =>
    getObituaryIndexById(obituaries, getObituaryIdFromPath())
  );

  useEffect(() => {
    setCurrentIndex(getObituaryIndexById(obituaries, getObituaryIdFromPath()));
  }, [obituaries]);

  useEffect(() => {
    const syncIndexFromPath = () => {
      setCurrentIndex(getObituaryIndexById(obituaries, getObituaryIdFromPath()));
    };

    window.addEventListener("popstate", syncIndexFromPath);
    window.addEventListener("locationchange", syncIndexFromPath);

    return () => {
      window.removeEventListener("popstate", syncIndexFromPath);
      window.removeEventListener("locationchange", syncIndexFromPath);
    };
  }, [obituaries]);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [currentIndex]);

  const currentObituary = obituaries[currentIndex];
  const hasPrev = currentIndex > 0;
  const hasNext = currentIndex < obituaries.length - 1;

  const navigateToObituary = (id) => {
    window.history.pushState({}, "", `/news/obituary/${id}`);
    window.dispatchEvent(new Event("locationchange"));
  };

  const handlePrevClick = () => {
    if (hasPrev) {
      navigateToObituary(obituaries[currentIndex - 1].id);
    }
  };

  const handleNextClick = () => {
    if (hasNext) {
      navigateToObituary(obituaries[currentIndex + 1].id);
    }
  };

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#obituary");
    window.dispatchEvent(new Event("locationchange"));
  };

  if (!currentObituary) {
    return (
      <>
        <ObituaryViewSubVisual />
        <div className="sub-content" id="content">
          <section className="obituary board-view">
            <div className="wrap-narrow">
              <p>부고 내용을 불러오는 중입니다...</p>
            </div>
          </section>
        </div>
      </>
    );
  }

  return (
    <>
      <ObituaryViewSubVisual />
      <div className="sub-content" id="content">
        <section className="obituary board-view">
          <div className="wrap-narrow">
            <ObituaryViewContent title={currentObituary.title} content={currentObituary.content} />
            <ObituaryViewNavigation
              onPrevClick={handlePrevClick}
              onNextClick={handleNextClick}
              onListClick={handleListClick}
              hasPrev={hasPrev}
              hasNext={hasNext}
            />
          </div>
        </section>
      </div>
    </>
  );
}
