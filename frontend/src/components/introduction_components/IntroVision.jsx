import "./css/IntroVision.css";

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
  const isEn = language === "en";
  const cards = visionStatements
      .slice()
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
      .map((s) => ({
        title: isEn ? (s.title_en || s.title) : s.title,
        points: String(isEn ? (s.points_en || s.points || "") : (s.points || ""))
          .split("\n")
          .map((l) => l.trim())
          .filter(Boolean),
      }));
  const copy = INTRO_VISION_COPY[language] || INTRO_VISION_COPY.kr;

  return (
    <section id="introduction01" className="intro-vision">
      <div className="wrap-narrow">
                <div className="intro-vision__head" data-ani="top">
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
            <article className="vision-card" data-ani="top" key={idx}>
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
