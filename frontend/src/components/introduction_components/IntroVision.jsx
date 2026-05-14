import "./css/IntroVision.css";

const FALLBACK_VISION = [
  {
    title: "예배 공동체",
    points: [
      "찬양과 설교, 설교후 찬양, 결단의 흐름이 되는 역동적 예배",
      "각 예배의 차별화를 통한 영적 필요충족",
      "예배팀을 세우는 훈련과 예배 중보기도 활성화",
      "가정, 전세대가 같이 드리는 예배",
    ],
  },
  {
    title: "목양 공동체",
    points: [
      "담임목사와 순장들의 깊은관계 속 동역자화",
      "'한 사람' 철학을 통한 깊은 성도 목양",
      "간증과 기쁨의 스토리가 흐르는 교회",
      "공동체 내에서의 치유와 성장 중점",
    ],
  },
  {
    title: "훈련 공동체",
    points: [
      "말씀으로 사람을 세우는 교회",
      "다음세대를 위한 체계적 지속적 훈련",
      "교회같은 가정을 이루는 가정 제자훈련 (Gospel Project / Family talk)",
    ],
  },
  {
    title: "미셔널 공동체",
    points: [
      "Glocal (Global + Local) 섬김과 지속적 선교",
      "전략 선교지역에 대한 지속적 선교",
      "가족선교 및 다음세대 선교를 통한 선교적 교회",
    ],
  },
];

const FALLBACK_VISION_EN = [
  {
    title: "Worship Community",
    points: [
      "A dynamic worship flow from praise to sermon, response praise, and commitment",
      "Meeting spiritual needs through distinct worship services",
      "Training worship teams and strengthening intercessory prayer during worship",
      "Family and all generations worshiping together",
    ],
  },
  {
    title: "Shepherding Community",
    points: [
      "Co-laboring through deep relationships between the senior pastor and cell leaders",
      "Deep pastoral care through the 'one person' philosophy",
      "A church where testimonies and stories of joy are shared",
      "A community focused on healing and growth",
    ],
  },
  {
    title: "Training Community",
    points: [
      "A church that builds people through the Word",
      "Systematic and continuous training for the next generation",
      "Family discipleship that builds church-like homes (Gospel Project / Family Talk)",
    ],
  },
  {
    title: "Missional Community",
    points: [
      "Glocal (Global + Local) service and ongoing mission",
      "Sustained mission work in strategic mission regions",
      "A missional church through family mission and next-generation mission",
    ],
  },
];

const INTRO_VISION_COPY = {
  kr: {
    heading: "밀알 교회는...",
    centerTitle: "밀알교회",
  },
  en: {
    heading: "Milal Church Is...",
    centerTitle: "MILAL",
  },
};

export default function IntroVision({ visionStatements = [], language = "kr" }) {
  const cardsKr = visionStatements.length
    ? visionStatements
        .slice()
        .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
        .map((s) => ({
          title: s.title,
          points: String(s.points || "")
            .split("\n")
            .map((l) => l.trim())
            .filter(Boolean),
        }))
    : FALLBACK_VISION;
  const copy = INTRO_VISION_COPY[language] || INTRO_VISION_COPY.kr;
  const cards = language === "en" ? FALLBACK_VISION_EN : cardsKr;

  return (
    <section id="introduction01" className="intro-vision">
      <div className="wrap-narrow">
                <div className="intro-vision__head">
          <h3 data-heading="5xl">{copy.heading}</h3>
        </div>
        
        <div className="intro-vision__cards">
          <div className="intro-vision__bg-ellipse" aria-hidden="true">
            <img src="/images/sub/01-introduction/vision-ellipse-bg.png" alt="" />
          </div>
          <div className="intro-vision__circle" aria-hidden="true">
            <i /><i /><i />
          </div>

          {cards.map((card, idx) => (
            <article className="vision-card" key={idx}>
              <h4 className="vision-card__title" data-heading="2xl">{card.title}</h4>
              <ul data-list="dot">
                {card.points.map((point, pidx) => (
                  <li key={pidx}>{point}</li>
                ))}
              </ul>
            </article>
          ))}

          <div className="intro-vision__box">
            <img src="/images/common/vision-logo.png" alt="" aria-hidden="true" />
            <h6>{copy.centerTitle}</h6>
          </div>
        </div>
      </div>
    </section>
  );
}
