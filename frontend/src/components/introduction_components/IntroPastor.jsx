import "./css/IntroPastor.css";

const CAREER = [
  "서강대학교 경영학과 졸업",
  "총신대학교 신학대학원 졸업",
  "Southern Baptist Theological Seminary 목회학 박사",
  "현) Toronto KOSTA 이사",
  "현) Love Toronto 이사장",
];

export default function IntroPastor() {
  return (
    <section id="introduction02" className="intro-pastor">
      <div className="intro-pastor__inr">

        <div className="intro-pastor__photo">
          <figure>
            <img src="/images/sub/01-introduction/pastor-photo.jpg" alt="담임목사 사진" />
          </figure>
        </div>

        <div className="intro-pastor__cont">
          <h3 className="intro-pastor__title" data-heading="5xl">
            복음으로 하나 되어,<br />세상으로 나아가는 교회
          </h3>
          <div className="intro-pastor__texts">
            <p className="intro-pastor__text">밀알교회에 오신 것을 환영합니다.</p>
            <p className="intro-pastor__text">
              밀알교회는 캐나다 토론토에 위치한 해외한인장로회(KPCA) 소속 <br />
              장로교회입니다. 저희 교회는 교회같은 가정, 가정같은 교회를 꿈꾸며 <br />
              하늘의 복을 받아 세상의 복을 나누는 교회가 되길 꿈꾸는 교회입니다.
            </p>
            <p className="intro-pastor__text">
              예배, 목양, 훈련, 미셔널 공동체를 이루며 제자의 삶을 통해 <br />
              복음을 증거하고 세상을 변화시키며 훈련된 증인으로 파송되어 <br />
              세상과 삶의 현장에 하나님 나라를 확장해가는 미셔널 공동체인 교회입니다.
            </p>
            <p className="intro-pastor__text">
              공동체 안에 있을 때 사람은 성장합니다. <br />
              성장하는 귀한 공동체로 여러분을 초대합니다.
            </p>
          </div>
          <div className="intro-pastor__divider" aria-hidden="true" />
          <h4 className="intro-pastor__name" data-heading="2xl">담임목사 박형일</h4>
          <div className="intro-pastor__career">
            <div className="intro-pastor__career-head">
              <i className="intro-pastor__career-logo" aria-hidden="true" />
              <p className="intro-pastor__career-title">약력</p>
            </div>
            <ul data-list="dot">
              {CAREER.map((item, idx) => <li key={idx}>{item}</li>)}
            </ul>
          </div>
        </div>

      </div>
    </section>
  );
}
