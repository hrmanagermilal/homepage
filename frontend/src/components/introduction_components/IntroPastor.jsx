import "./css/IntroPastor.css";

const INTRO_PASTOR_COPY = {
  kr: {
    photoAlt: "담임목사 사진",
    titleLines: ["복음으로 하나 되어,", "세상으로 나아가는 교회"],
    paragraphs: [
      ["밀알교회에 오신 것을 환영합니다."],
      [
        "밀알교회는 캐나다 토론토에 위치한 해외한인장로회(KPCA) 소속",
        "장로교회입니다. 저희 교회는 교회같은 가정, 가정같은 교회를 꿈꾸며",
        "하늘의 복을 받아 세상의 복을 나누는 교회가 되길 꿈꾸는 교회입니다.",
      ],
      [
        "예배, 목양, 훈련, 미셔널 공동체를 이루며 제자의 삶을 통해",
        "복음을 증거하고 세상을 변화시키며 훈련된 증인으로 파송되어",
        "세상과 삶의 현장에 하나님 나라를 확장해가는 미셔널 공동체인 교회입니다.",
      ],
      [
        "공동체 안에 있을 때 사람은 성장합니다.",
        "성장하는 귀한 공동체로 여러분을 초대합니다.",
      ],
    ],
    pastorRole: "담임목사",
    pastorName: "박형일",
    careerTitle: "약력",
    career: [
      "서강대학교 경영학과 졸업",
      "총신대학교 신학대학원 졸업",
      "Southern Baptist Theological Seminary 목회학 박사",
      "현) Toronto KOSTA 이사",
      "현) Love Toronto 이사장",
    ],
  },
  en: {
    photoAlt: "Senior Pastor portrait",
    titleLines: ["United in the Gospel,", "Sent Out to the World"],
    paragraphs: [
      ["Welcome to Milal Church."],
      [
        "Milal Church is a Presbyterian church in Toronto, Canada, under KPCA.",
        "We dream of homes like churches and a church like home,",
        "receiving heaven's blessing to share blessing with the world.",
      ],
      [
        "As a worshiping, shepherding, training, and missional community,",
        "we proclaim the Gospel through a disciple's life, transform the world,",
        "and are sent as trained witnesses to expand God's Kingdom in daily life.",
      ],
      [
        "People grow when they belong to a community.",
        "We invite you into this growing and precious community.",
      ],
    ],
    pastorRole: "Senior Pastor",
    pastorName: "Hyung Il Park",
    careerTitle: "Career",
    career: [
      "B.B.A., Sogang University",
      "M.Div., Chongshin Theological Seminary",
      "D.Min., The Southern Baptist Theological Seminary",
      "Current) Board Member, Toronto KOSTA",
      "Current) Chairman, Love Toronto",
    ],
  },
};

export default function IntroPastor({ language = "kr" }) {
  const copy = INTRO_PASTOR_COPY[language] || INTRO_PASTOR_COPY.kr;

  return (
    <section id="introduction02" className="intro-pastor">
      <div className="intro-pastor__inr">

        <div className="intro-pastor__photo">
          <figure>
            <img src="/images/sub/01-introduction/pastor-photo.jpg" alt={copy.photoAlt} />
          </figure>
        </div>

        <div className="intro-pastor__cont">
          <h3 className="intro-pastor__title" data-heading="5xl">
            {copy.titleLines[0]}<br />{copy.titleLines[1]}
          </h3>
          <div className="intro-pastor__texts">
            {copy.paragraphs.map((lines, idx) => (
              <p className="intro-pastor__text" key={idx}>
                {lines.map((line, lineIdx) => (
                  <span key={lineIdx}>
                    {line}
                    {lineIdx < lines.length - 1 ? <br /> : null}
                  </span>
                ))}
              </p>
            ))}
          </div>
          <div className="intro-pastor__divider" aria-hidden="true" />
          <h4 className="intro-pastor__name" data-heading="2xl">{copy.pastorRole} {copy.pastorName}</h4>
          <div className="intro-pastor__career">
            <div className="intro-pastor__career-head">
              <i className="intro-pastor__career-logo" aria-hidden="true" />
              <p className="intro-pastor__career-title">{copy.careerTitle}</p>
            </div>
            <ul data-list="dot">
              {copy.career.map((item, idx) => <li key={idx}>{item}</li>)}
            </ul>
          </div>
        </div>

      </div>
    </section>
  );
}
