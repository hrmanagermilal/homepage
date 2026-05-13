import { useEffect, useState, useMemo } from "react";
import "./css/SubPage.css";
import "./css/ObituaryViewPage.css";
import ObituaryViewSubVisual from "./obituary_components/ObituaryViewSubVisual";
import ObituaryViewContent from "./obituary_components/ObituaryViewContent";
import ObituaryViewNavigation from "./obituary_components/ObituaryViewNavigation";

const OBITUARY_DATA = [
  {
    id: 1,
    title: "박주희 집사(김주환 집사)<br>모친 소천(영광 2순)",
    description: "강혜숙 권사님(딸: 박주희 집사, 사위: 김주환 집사) 께서 2026년 4월 17일(금) 향년 84세로",
    content: "강혜숙 권사님(딸: 박주희 집사, 사위: 김주환 집사) 께서 2026년 4월 17일(금) 향년 84세로 소천하셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 04. 17",
  },
  {
    id: 2,
    title: "이효숙 성도 부친 소천<br>(청장년 1순)",
    description: "이무남 성도님(딸: 이효숙 성도)께서 2026년 4월 12일(주일), 향년 82세로 하나님의 부르심을 받으셨습니다.",
    content: "이무남 성도님(딸: 이효숙 성도)께서 2026년 4월 12일(주일), 향년 82세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 04. 01",
  },
  {
    id: 3,
    title: "이진아(윤석원)집사 부친 소천<br>(온유 4순)",
    description: "이건대 장로님(딸: 이진아 집사, 사위: 윤석원 집사)께서 2026년 2월 19일(목), 향년 81세로 하나님의 부르심을 받으셨습니다.",
    content: "이건대 장로님(딸: 이진아 집사, 사위: 윤석원 집사)께서 2026년 2월 19일(목), 향년 81세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 03. 08",
  },
  {
    id: 4,
    title: "김일환(이순녀)집사 소천(모세회)",
    description: "김일환 집사님(이순녀 명예권사)께서 2026년 3월 2일(월) 오후 1시, 향년 98세로 하나님의 부름을 받으셨습니다.",
    content: "김일환 집사님(이순녀 명예권사)께서 2026년 3월 2일(월) 오후 1시, 향년 98세로 하나님의 부름을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 03. 03",
  },
  {
    id: 5,
    title: "서예원 집사 부친 소천(충성 5순)",
    description: "서재호 성도님(딸: 서예원 집사)께서 2026년 2월 19(목) 오전 6시 20분, 향년 84세로 하나님의 부르심을 받으셨습니다.",
    content: "서재호 성도님(딸: 서예원 집사)께서 2026년 2월 19(목) 오전 6시 20분, 향년 84세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 02. 18",
  },
  {
    id: 6,
    title: "조양임 집사(심택)모친 소천<br>(기쁨 4순)",
    description: "유명자 집사님(딸: 조양임 집사, 사위: 심택 집사)께서 2026년 2월 15일(주일), 향년 85세로 하나님의 부르심을 받으셨습니다.",
    content: "유명자 집사님(딸: 조양임 집사, 사위: 심택 집사)께서 2026년 2월 15일(주일), 향년 85세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.",
    date: "2026. 02. 15",
  },
];

export default function ObituaryViewPage() {
  const [currentIndex, setCurrentIndex] = useState(0);

  // Extract ID from URL path
  const idFromPath = useMemo(() => {
    const match = window.location.pathname.match(/\/news\/obituary\/(\d+)/);
    return match ? Number(match[1]) : 1;
  }, []);

  // Initialize current index based on URL ID
  useEffect(() => {
    const index = OBITUARY_DATA.findIndex((item) => item.id === idFromPath);
    if (index !== -1) {
      setCurrentIndex(index);
    }
  }, [idFromPath]);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [currentIndex]);

  const currentObituary = OBITUARY_DATA[currentIndex];
  const hasPrev = currentIndex > 0;
  const hasNext = currentIndex < OBITUARY_DATA.length - 1;

  const handlePrevClick = () => {
    if (hasPrev) {
      const prevId = OBITUARY_DATA[currentIndex - 1].id;
      window.location.pathname = `/news/obituary/${prevId}`;
    }
  };

  const handleNextClick = () => {
    if (hasNext) {
      const nextId = OBITUARY_DATA[currentIndex + 1].id;
      window.location.pathname = `/news/obituary/${nextId}`;
    }
  };

  const handleListClick = () => {
    window.location.href = "/news/obituary";
  };

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
