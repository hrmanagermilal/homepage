import IntroVision from "./introduction_components/IntroVision";
import IntroPastor from "./introduction_components/IntroPastor";
import IntroMinisters from "./introduction_components/IntroMinisters";
import IntroPartner from "./introduction_components/IntroPartner";
import "./css/IntroductionPage.css";
import "./css/SubPage.css";

const LNB_ITEMS = [
  { label: "교회비전", href: "#introduction01" },
  { label: "담임목사", href: "#introduction02" },
  { label: "섬기는 분들", href: "#introduction03" },
  { label: "함께하는 교회", href: "#introduction04" },
];

function SubVisual() {
  return (
    <section className="sub-visual" aria-label="Introduction 서브 비주얼">
      <div className="sub-visual__bg" aria-hidden="true">
        <figure className="sub-visual__bg-img intro-bg" />
      </div>
      <div className="sub-visual__ellipse" aria-hidden="true">
        <img src="/images/main/main-visual-ellipse.svg" alt="" />
      </div>
      <div className="sub-visual__cont">
        <nav className="sub-visual__lnb" aria-label="현재 위치">
          <a className="sub-visual__lnb-home" href="/" aria-label="홈">
            <img src="/images/common/ic-nav-w.svg" alt="" />
          </a>
          <span className="sub-visual__lnb-sep" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
              <path d="M3.46447 1.46447L7 5L3.46447 8.53553" stroke="white" strokeOpacity="0.2" strokeWidth="2" strokeLinecap="round"/>
            </svg>
          </span>
          <span className="sub-visual__lnb-text">Introduction</span>
        </nav>
        <h2 className="sub-visual__title">Introduction</h2>
      </div>
      <div className="sub-visual__scroll-down" aria-hidden="true">
        <i /><span>SCROLL DOWN</span>
      </div>
    </section>
  );
}

function SubLnb() {
  return (
    <div className="lnb-wrap">
      <nav className="lnb" aria-label="Introduction 메뉴">
        {LNB_ITEMS.map((item, idx) => (
          <a key={idx} className={`lnb__btn${idx === 0 ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
             href={item.href}>
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

export default function IntroductionPage({ togetherItems = [], members = [], visionStatements = [] }) {
  return (
    <>
      <SubVisual />
      <div className="sub-content" id="content">
        <SubLnb />
        <IntroVision visionStatements={visionStatements} />
        <IntroPastor />
        <IntroMinisters members={members} />
        <section id="introduction04">
          <IntroPartner togetherItems={togetherItems} />
        </section>
      </div>
    </>
  );
}
