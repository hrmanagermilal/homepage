import { useEffect, useState } from "react";
import "./css/SubPage.css";
import "./css/NoticeViewPage.css";
import NoticeViewSubVisual from "./notice_components/NoticeViewSubVisual";
import NoticeViewContent from "./notice_components/NoticeViewContent";
import NoticeViewNavigation from "./notice_components/NoticeViewNavigation";

function getNoticeIdFromPath() {
  const match = window.location.pathname.match(/\/news\/notice\/(\d+)/);
  return match ? Number(match[1]) : 1;
}

function getNoticeIndexById(notices, id) {
  const index = notices.findIndex((item) => item.id === id);
  return index !== -1 ? index : 0;
}

export default function NoticeViewPage({ notices = [] }) {
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (notices.length > 0) {
      setCurrentIndex(getNoticeIndexById(notices, getNoticeIdFromPath()));
    }
  }, [notices]);

  useEffect(() => {
    const syncIndexFromPath = () => {
      setCurrentIndex(getNoticeIndexById(notices, getNoticeIdFromPath()));
    };
    window.addEventListener("popstate", syncIndexFromPath);
    window.addEventListener("locationchange", syncIndexFromPath);
    return () => {
      window.removeEventListener("popstate", syncIndexFromPath);
      window.removeEventListener("locationchange", syncIndexFromPath);
    };
  }, [notices]);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [currentIndex]);

  const currentNotice = notices[currentIndex];
  const hasPrev = currentIndex > 0;
  const hasNext = currentIndex < notices.length - 1;

  const navigateToNotice = (id) => {
    window.history.pushState({}, "", `/news/notice/${id}`);
    window.dispatchEvent(new Event("locationchange"));
  };

  const handlePrevClick = () => {
    if (hasPrev) {
      const prevId = notices[currentIndex - 1].id;
      navigateToNotice(prevId);
    }
  };

  const handleNextClick = () => {
    if (hasNext) {
      const nextId = notices[currentIndex + 1].id;
      navigateToNotice(nextId);
    }
  };

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#notice");
    window.dispatchEvent(new Event("locationchange"));
  };

  if (!currentNotice) {
    return (
      <>
        <NoticeViewSubVisual />
        <div className="sub-content" id="content">
          <section className="notice board-view">
            <div className="wrap-narrow">공지 내용을 불러오는 중입니다...</div>
          </section>
        </div>
      </>
    );
  }

  return (
    <>
      <NoticeViewSubVisual />
      <div className="sub-content" id="content">
        <section className="notice board-view">
          <div className="wrap-narrow">
            <NoticeViewContent
              title={currentNotice.title}
              author={currentNotice.author}
              date={currentNotice.date}
              views={currentNotice.views}
              content={currentNotice.content}
              image={currentNotice.image}
              link={currentNotice.link}
              link_text={currentNotice.link_text}
            />
            <NoticeViewNavigation
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
