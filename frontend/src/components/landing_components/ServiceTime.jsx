import "./css/ServiceTime.css";

export default function ServiceTime({ departments = [], section = null }) {
  const eduRows = departments.length
    ? departments.slice(0, 5).map((dep) => [dep.name || "교육부", dep.worship_time || "시간 안내 예정"])
    : [
        ["영유아부", "오전 9:45 / 오전 11:45"],
        ["유치부", "오전 9:45 / 오전 11:45"],
        ["아동부", "오전 9:45 / 오전 11:45"],
        ["청소년부 한국어권(KM)", "오전 9:45"],
        ["청소년부 영어권(EM)", "오전 11:45"],
      ];

  return (
    <section id="worship" className="main-worship">
      <div className="wrap">
        <div className="main-title" data-ani="top">
          <h2 data-heading="5xl" className="main-title__heading">예배 시간 안내</h2>
          <p className="main-title__sub">
            주일 1~3부 예배, 청년부/교육부 예배, 그리고 주중예배가 있습니다.<br />
            함께 예배하는 축복의 자리로 당신을 초대합니다.
          </p>
        </div>

        <div className="worship-list" data-grid="3">
          {/* 주일 예배 */}
          <div className="worship-card" data-ani="top">
            <div className="worship-card__head">
              <div className="worship-card__logo"><img src="/images/main/worship-card-logo.png" alt="" /></div>
              <h3 data-heading="lg">주일 예배</h3>
              <span className="worship-card__en" aria-hidden="true">SUNDAY SERVICE</span>
            </div>
            <table className="worship-card__table">
              <caption className="hidden">주일 예배 시간표</caption>
              <thead><tr><th scope="col">구분</th><th scope="col">시간 및 일시</th></tr></thead>
              <tbody>
                <tr><th scope="row">1부</th><td>오전 8:00</td></tr>
                <tr><th scope="row">2부</th><td>오전 9:45</td></tr>
                <tr><th scope="row">3부</th><td>오전 11:45</td></tr>
                <tr><th scope="row">4부(청년)</th><td>오후 2:00</td></tr>
              </tbody>
            </table>
          </div>

          {/* 주중 예배 */}
          <div className="worship-card worship-card--weekly" data-ani="top">
            <div className="worship-card__head">
              <div className="worship-card__logo"><img src="/images/main/worship-card-logo.png" alt="" /></div>
              <h3 data-heading="lg">주중 예배</h3>
              <span className="worship-card__en" aria-hidden="true">Midweek Service</span>
            </div>
            <table className="worship-card__table">
              <caption className="hidden">주중 예배 시간표</caption>
              <thead><tr><th scope="col">구분</th><th scope="col">시간 및 일시</th></tr></thead>
              <tbody>
                <tr>
                  <th scope="row">새벽 기도회</th>
                  <td>
                    <div className="worship-card__times">
                      <div className="worship-card__time-row">
                        <span className="worship-badge">평일</span>오전 6:00
                      </div>
                      <div className="worship-card__time-row">
                        <span className="worship-badge">토요일</span>오전 6:30
                      </div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th scope="row">수요 오전 예배</th>
                  <td>
                    <div className="worship-card__time-row">
                      <span className="worship-badge">수요일</span>오전 10:30
                    </div>
                  </td>
                </tr>
                <tr>
                  <th scope="row">금요 찬양 집회</th>
                  <td>
                    <div className="worship-card__time-row">
                      <span className="worship-badge">금요일</span>오후 7:30
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          {/* 교육부 예배 */}
          <div className="worship-card worship-card--edu" data-ani="top">
            <div className="worship-card__head">
              <div className="worship-card__logo"><img src="/images/main/worship-card-logo.png" alt="" /></div>
              <h3 data-heading="lg">교육부 예배</h3>
              <span className="worship-card__en" aria-hidden="true">Children &amp; Youth Service</span>
            </div>
            <table className="worship-card__table">
              <caption className="hidden">교육부 예배 시간표</caption>
              <thead><tr><th scope="col">구분</th><th scope="col">시간 및 일시</th></tr></thead>
              <tbody>
                {eduRows.map((row) => (
                  <tr key={row[0]}><th scope="row">{row[0]}</th><td>{row[1]}</td></tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  );
}
