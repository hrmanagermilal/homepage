import "./css/IntroPartner.css";

const FALLBACK_PARTNERS = [
  { name: "하늘씨앗 교회", href: "https://www.hsctoronto.com/", img: "/images/sub/01-introduction/partner-logo-01.png" },
  { name: "bridgewaychurch", href: "https://bridgewaychurch.ca/", img: "/images/sub/01-introduction/partner-logo-02.png" },
  { name: "순례길교회", href: "https://jcchurch.ca/", img: "/images/sub/01-introduction/partner-logo-03.png" },
];

export default function IntroPartner({ togetherItems = [] }) {
  const partners = togetherItems.length
    ? togetherItems.map((item) => ({
        name: item.title,
        href: item.link || "#",
        img: item.image || item.image_url || item.file_path || "",
      }))
    : FALLBACK_PARTNERS;

  return (
    <section className="intro-partner">
      <div className="intro-partner__inr">
        <div className="intro-partner__bg-ellipse" aria-hidden="true">
          <img src="/images/main/main-visual-ellipse.svg" alt="" />
        </div>
        <div className="intro-partner__cont">
          <h3 className="intro-partner__title" data-heading="5xl">함께하는 교회</h3>
          <p className="intro-partner__sub">
            복음 안에서 하나 된 교회들이 함께합니다. <br />
            서로 다른 자리에서, 같은 방향을 바라보며 세상을 향해 나아가는 믿음의 동역자들을 소개합니다.
          </p>
          <ul className="intro-partner__logos">
            {partners.map((partner, idx) => (
              <li key={idx} className="partner-logo">
                <a href={partner.href} target="_blank" rel="noopener noreferrer">
                  <img src={partner.img} alt={partner.name} />
                </a>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </section>
  );
}
