import { useState } from "react";
import "./css/MinistryYangyuk.css";

const FAQ_ITEMS = [
  {
    question: "밀알교회의 순모임은 어떻게 진행되나요?",
    answerType: "circles",
  },
  {
    question: "순모임을 통해 얻는 유익은 무엇인가요?",
    answerType: "text",
  },
];

const CIRCLES = [
  {
    title: "삶의 나눔",
    body: (
      <>
        순원들 삶의 나눔이 있습니다.
      </>
    ),
  },
  {
    title: "중보 기도",
    body: (
      <>
        순원들이 나눈 삶을 가지고
        <br />
        서로를 위해 중보하는
        <br />
        기도시간이 있습니다.
      </>
    ),
  },
  {
    title: "성경적 적용",
    body: (
      <>
        단지 성경지식을 전달하는데 집중하기보다는,
        <br />
        성경의 진리가 우리 일상의 삶을
        <br />
        변화시키는 과정에 관심이 있습니다.
      </>
    ),
  },
];

export default function MinistryYangyuk() {
  const [openStates, setOpenStates] = useState([true, true]);

  const toggleFaq = (idx) => {
    setOpenStates((prev) => prev.map((isOpen, i) => (i === idx ? !isOpen : isOpen)));
  };

  return (
    <section className="ministry">
      <div className="wrap-narrow">
        <div className="ministry-faq">
          {FAQ_ITEMS.map((item, idx) => {
            const isOpen = openStates[idx];
            return (
              <div key={item.question} className={`ministry-faq__item${isOpen ? " is-open" : ""}`}>
                <button
                  className="ministry-faq__header"
                  type="button"
                  aria-expanded={isOpen}
                  onClick={() => toggleFaq(idx)}
                >
                  <div className="ministry-faq__q">
                    <span className="ministry-faq__q-prefix" aria-hidden="true">
                      Q.
                    </span>
                    <span className="ministry-faq__q-text">{item.question}</span>
                  </div>
                  <span className="ministry-faq__arrow" aria-hidden="true">
                    <img src="/images/sub/03-ministry/ic-chevron.svg" alt="" />
                  </span>
                </button>

                <div className="ministry-faq__body">
                  {item.answerType === "circles" ? (
                    <>
                      <div className="ministry-faq__answer">
                        <p>밀알교회 순모임은 하나의 교회로써 교회사역의 핵심을 이루는 사역입니다.</p>
                        <p>
                          밀알교회 순모임은 <strong>"말씀을 가지고 삶을 나누는 시간"</strong> 입니다.
                        </p>
                      </div>
                      <ul className="ministry-circles">
                        {CIRCLES.map((circle) => (
                          <li key={circle.title} className="ministry-circle">
                            <p className="ministry-circle__title">{circle.title}</p>
                            <p className="ministry-circle__body">{circle.body}</p>
                          </li>
                        ))}
                      </ul>
                    </>
                  ) : (
                    <p className="ministry-faq__answer-text">
                      혼자하는 신앙이 아닌 동역하여 믿음의 동역자를 만날수 있습니다.
                      <br />
                      터전을 떠나온 이민자의 삶으로써 어려움에서 위로와 사랑을 받을수 있습니다.
                      <br />
                      예배가 삶이되고 삶이 예배되는 기쁨을 함께 누릴수 있습니다.
                      <br />
                      순장, 순모와 함께 매 달 정기 모임을 통해 영적 습관를 하도록 돕고 공동체를 경험하여 사역으로 연결될 수 있도록 세워갑니다.
                    </p>
                  )}
                </div>
              </div>
            );
          })}
        </div>

        <div className="ministry-download">
          <div className="ministry-download__text">
            <p className="ministry-download__title">4월 순모임 교재 공유드립니다.</p>
            <p className="ministry-download__desc">PDF파일을 다운 받으셔서 순모임에 활용하세요.</p>
          </div>
          <a className="btn-basic-big btn-basic-big--trans ministry-download__btn" href="#">
            <i aria-hidden="true" />
            <span>PDF 다운로드</span>
          </a>
        </div>
      </div>
    </section>
  );
}
