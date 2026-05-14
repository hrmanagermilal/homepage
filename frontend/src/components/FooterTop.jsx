import "./css/FooterTop.css";

export default function FooterTop() {
  return (
    <section className="main-banner">
      <div className="main-banner__bg" aria-hidden="true">
        <img className="main-banner__bg-img" src="/images/main/banner-bg.png" alt="" />
        <div className="main-banner__bg-blur"></div>
        <div className="main-banner__bg-overlay"></div>
      </div>
      <div className="wrap main-banner__cont" data-ani="top">
        <h2 data-heading="5xl" className="main-banner__heading">교회같은 가정, 가정같은 교회</h2>
        <p className="main-banner__sub">
          교회같은 가정, 가정같은 교회를 꿈꾸며 하늘의 복을 받아<br />
          세상의 복을 나누는 교회가 되길 꿈꾸는 교회입니다.
        </p>
        <a className="btn-basic btn-basic--trans" href="/introduction">밀알교회 더 알아보기</a>
      </div>
    </section>
  );
}
