import { useMemo } from "react";
import "./css/ServiceTime.css";

// Fallback data shown when API hasn't returned yet or is empty
const FALLBACK = {
  sunday: [
    { name: "1부",       day: null, time: "오전 8:00" },
    { name: "2부",       day: null, time: "오전 9:45" },
    { name: "3부",       day: null, time: "오전 11:45" },
    { name: "4부(청년)", day: null, time: "오후 2:00" },
  ],
  weekly: [
    { name: "새벽 기도회",    day: "평일",   time: "오전 6:00" },
    { name: "새벽 기도회",    day: "토요일", time: "오전 6:30" },
    { name: "수요 오전 예배", day: "수요일", time: "오전 10:30" },
    { name: "금요 찬양 집회", day: "금요일", time: "오후 7:30" },
  ],
  edu: [
    { name: "미라클 영유아부",               day: null, time: "오전 9:45 / 오전 11:45" },
    { name: "조이 유치부",                 day: null, time: "오전 9:45 / 오전 11:45" },
    { name: "카리스 아동부",                 day: null, time: "오전 9:45 / 오전 11:45" },
    { name: "청소년부 한국어권(KM)",  day: null, time: "오전 9:45" },
    { name: "청소년부 영어권(EM)",    day: null, time: "오전 11:45" },
  ],
};

/**
 * Group an array of rows by the `name` field.
 * Returns an array of { name, rows[] } objects preserving first-seen order.
 */
function groupByName(rows) {
  const map = new Map();
  rows.forEach((row) => {
    if (!map.has(row.name)) map.set(row.name, []);
    map.get(row.name).push(row);
  });
  return Array.from(map.entries()).map(([name, rows]) => ({ name, rows }));
}

export default function ServiceTime({ serviceTimes = [], section = null }) {
  const sunday = useMemo(
    () => serviceTimes.filter((s) => s.category === "주일예배"),
    [serviceTimes]
  );
  const weekly = useMemo(
    () => serviceTimes.filter((s) => s.category === "주중예배"),
    [serviceTimes]
  );
  const edu = useMemo(
    () => serviceTimes.filter((s) => s.category === "교육부예배"),
    [serviceTimes]
  );

  const sundayRows  = sunday.length  ? sunday  : FALLBACK.sunday;
  const weeklyRows  = weekly.length  ? weekly  : FALLBACK.weekly;
  const eduRows     = edu.length     ? edu     : FALLBACK.edu;

  const weeklyGrouped = groupByName(weeklyRows);

  const title    = section?.title    ?? "예배 시간 안내";
  const subtitle = section?.subtitle ?? "주일 1~3부 예배, 청년부/교육부 예배, 그리고 주중예배가 있습니다.\n함께 예배하는 축복의 자리로 당신을 초대합니다.";
  return (
    <section id="worship" className="main-worship">
      <div className="wrap">
        <div className="main-title" data-ani="top">
          <h2 data-heading="5xl" className="main-title__heading">{title}</h2>
          <p className="main-title__sub">
            {subtitle.split("\n").map((line, i) => (
              <span key={i}>{line}{i < subtitle.split("\n").length - 1 && <br />}</span>
            ))}
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
                {sundayRows.map((row) => (
                  <tr key={row.name}>
                    <th scope="row">{row.name}</th>
                    <td>{row.time}</td>
                  </tr>
                ))}
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
                {weeklyGrouped.map(({ name, rows }) => (
                  <tr key={name}>
                    <th scope="row">{name}</th>
                    <td>
                      {rows.length === 1 && !rows[0].day ? (
                        rows[0].time
                      ) : (
                        <div className="worship-card__times">
                          {rows.map((r, i) => (
                            <div key={i} className="worship-card__time-row">
                              {r.day && <span className="worship-badge">{r.day}</span>}
                              {r.time}
                            </div>
                          ))}
                        </div>
                      )}
                    </td>
                  </tr>
                ))}
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
                  <tr key={row.name}>
                    <th scope="row">{row.name}</th>
                    <td>{row.time}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  );
}
