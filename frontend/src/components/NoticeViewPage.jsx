import { useEffect, useState } from "react";
import "./css/SubPage.css";
import "./css/NoticeViewPage.css";
import NoticeViewSubVisual from "./notice_components/NoticeViewSubVisual";
import NoticeViewContent from "./notice_components/NoticeViewContent";
import NoticeViewNavigation from "./notice_components/NoticeViewNavigation";

const NOTICE_DATA = [
  {
    id: 1,
    title: "2026년 1월 주보 및 교회 일정 안내",
    author: "성전관리",
    date: "2026. 01. 01",
    views: 245,
    content: "2026년 새해를 맞이하여 주간 예배 일정 및 각 부서 활동 계획을 공지드립니다.<br><br>주일예배: 오전 11시<br>수요기도회: 오후 7시 30분<br>새벽기도: 오전 5시 30분<br><br>자세한 일정은 첨부된 주보를 참고해주시기 바랍니다.",
  },
  {
    id: 2,
    title: "새해맞이 성령집회 안내",
    author: "행정부",
    date: "2025. 12. 28",
    views: 312,
    content: "2026년 새해를 맞이하여 특별 성령집회를 개최합니다.<br><br>일시: 2026년 1월 1일 ~ 3일<br>시간: 오후 7시 30분<br>장소: 메인 예배당<br><br>모든 성도들의 적극적인 참여를 부탁드립니다.",
  },
  {
    id: 3,
    title: "교회 건축 기금 모금 안내",
    author: "재정부",
    date: "2025. 12. 20",
    views: 189,
    content: "교회 새 건물 건축을 위한 기금 모금을 시작합니다.<br><br>목표액: $500,000<br>모금 기간: 2026년 1월 ~ 12월<br>헌금 방법: 온라인 헌금, 주일예배 헌금, E-Transfer<br><br>더 자세한 정보는 사무실에 문의해주시기 바랍니다.",
  },
  {
    id: 4,
    title: "2026년 사역자 간담회 개최",
    author: "성전관리",
    date: "2025. 12. 15",
    views: 156,
    content: "2026년도 교회 사역 계획 수립을 위한 사역자 간담회를 개최합니다.<br><br>일시: 2025년 12월 22일 (화요일)<br>시간: 오후 7시<br>장소: 교육관<br><br>모든 사역자의 참석을 부탁드립니다.",
  },
  {
    id: 5,
    title: "성탄절 특별예배 안내",
    author: "행정부",
    date: "2025. 12. 10",
    views: 421,
    content: "2025년 성탄절을 맞이하여 특별예배를 드립니다.<br><br>일시: 2025년 12월 25일 오전 10시<br>장소: 메인 예배당<br><br>본 예배 이후 성찬식과 교제의 시간을 갖을 예정입니다. 모든 성도들의 참석을 기원합니다.",
  },
  {
    id: 6,
    title: "2025년 감사절 감사예배 안내",
    author: "성전관리",
    date: "2025. 11. 28",
    views: 267,
    content: "2025년 추수감사절을 맞이하여 감사예배를 드립니다.<br><br>일시: 2025년 11월 27일 오전 11시<br>장소: 메인 예배당<br><br>감사의 물품은 다음과 같습니다: 곡식, 과일, 채소 등<br>감사헌금은 사회복지사업에 사용될 예정입니다.",
  },
  {
    id: 7,
    title: "추수감사절 헌금 안내",
    author: "재정부",
    date: "2025. 11. 20",
    views: 198,
    content: "추수감사절 감사헌금 안내드립니다.<br><br>헌금은 다음과 같은 용도로 사용됩니다:<br>- 지역사회 나눔 사업<br>- 선교사 지원<br>- 어려운 이웃 돕기<br><br>많은 참여 부탁드립니다.",
  },
  {
    id: 8,
    title: "교회 건물 리모델링 공사 안내",
    author: "성전관리",
    date: "2025. 11. 15",
    views: 334,
    content: "교회 건물의 노후 시설 개선을 위한 리모델링 공사를 시작합니다.<br><br>공사 기간: 2025년 11월 20일 ~ 2026년 2월 28일<br>공사 구간: 교육관, 복도, 주차장<br><br>공사 기간 중 불편을 드릴 수 있으니 양해 부탁드립니다.",
  },
  {
    id: 9,
    title: "미션 여행 팀 모집",
    author: "선교부",
    date: "2025. 11. 05",
    views: 145,
    content: "2026년 선교 여행에 참여할 팀을 모집합니다.<br><br>목적지: 베트남<br>일정: 2026년 6월 15일 ~ 21일<br>인원: 15명 (선착순)<br><br>자세한 정보는 선교부에 문의하시기 바랍니다.",
  },
  {
    id: 10,
    title: "2025년 겨울 성경학교 등록 안내",
    author: "교육부",
    date: "2025. 10. 28",
    views: 289,
    content: "2025년 겨울 성경학교 등록을 받고 있습니다.<br><br>일정: 2025년 12월 29일 ~ 2026년 1월 3일<br>대상: 미취학아동 ~ 중등부<br>시간: 오전 10시 ~ 오후 12시<br><br>온라인 등록: www.milalchurch.ca<br>문의: 교육부 (416) 000-0000",
  },
];

function getNoticeIdFromPath() {
  const match = window.location.pathname.match(/\/news\/notice\/(\d+)/);
  return match ? Number(match[1]) : 1;
}

function getNoticeIndexById(id) {
  const index = NOTICE_DATA.findIndex((item) => item.id === id);
  return index !== -1 ? index : 0;
}

export default function NoticeViewPage() {
  const [currentIndex, setCurrentIndex] = useState(() => getNoticeIndexById(getNoticeIdFromPath()));

  useEffect(() => {
    const syncIndexFromPath = () => {
      setCurrentIndex(getNoticeIndexById(getNoticeIdFromPath()));
    };

    window.addEventListener("popstate", syncIndexFromPath);
    window.addEventListener("locationchange", syncIndexFromPath);

    return () => {
      window.removeEventListener("popstate", syncIndexFromPath);
      window.removeEventListener("locationchange", syncIndexFromPath);
    };
  }, []);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [currentIndex]);

  const currentNotice = NOTICE_DATA[currentIndex];
  const hasPrev = currentIndex > 0;
  const hasNext = currentIndex < NOTICE_DATA.length - 1;

  const navigateToNotice = (id) => {
    window.history.pushState({}, "", `/news/notice/${id}`);
    window.dispatchEvent(new Event("locationchange"));
  };

  const handlePrevClick = () => {
    if (hasPrev) {
      const prevId = NOTICE_DATA[currentIndex - 1].id;
      navigateToNotice(prevId);
    }
  };

  const handleNextClick = () => {
    if (hasNext) {
      const nextId = NOTICE_DATA[currentIndex + 1].id;
      navigateToNotice(nextId);
    }
  };

  const handleListClick = () => {
    window.history.pushState({}, "", "/news/notice");
    window.dispatchEvent(new Event("locationchange"));
  };

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
