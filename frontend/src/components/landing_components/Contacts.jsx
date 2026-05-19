import { useState, useMemo, useEffect } from "react";
import "../css/Contacts.css";

const TABS = ["오시는 길 안내", "셔틀버스 안내", "주차장 안내"];
const MAP_SRC = "https://maps.google.com/maps?q=405+Gordon+Baker+Rd,+Toronto+ON+M2H+2S6,+Canada&output=embed&hl=ko";
const SHUTTLE_MAP_SRC = "https://maps.google.com/maps?q=16+Hendon+Ave,+North+York,+ON+M2M+1A2&output=embed&hl=ko";

const FALLBACK_SHUTTLE = [
  { direction: "finch_to_church", time: "오전 9시 15분",  service_label: "2부",          sort_order: 1 },
  { direction: "finch_to_church", time: "오전 11시 15분", service_label: "3부",          sort_order: 2 },
  { direction: "finch_to_church", time: "오후 1시 30분",  service_label: "4부",          sort_order: 3 },
  { direction: "church_to_finch", time: "오후 12시",      service_label: "2부",          sort_order: 1 },
  { direction: "church_to_finch", time: "오후 2시",       service_label: "3부",          sort_order: 2 },
  { direction: "church_to_finch", time: "오후 5시",       service_label: "4부, 청년예배", sort_order: 3 },
];

const FALLBACK_PARKING = [
  { id: 1, content: "건물 정문 앞 A 주차장과 동쪽 C주차장은 늘푸른 회원, 장애인, 임산부, 방문자, 18개월 이하의 자녀 동반가정을 위한 주차장입니다.\n그 외의 성도들은 건물 북쪽 B주차장과 남쪽 D주차장을 이용해주시기 바랍니다." },
  { id: 2, content: "1부 예배에 참석하시는 성도들 역시 해당 주차장에 주차해주시기 바랍니다." },
  { id: 3, content: "교회에서 공지하는 이외의 장소에 주차하시면 주차위반 티켓을 받으실 수 있으니 유의 바랍니다." },
  { id: 4, content: "출입구 쪽 주차는 진행에 방해가 되니 반드시 지정된 주차구역에만 주차해주시기 바랍니다." },
];

export default function Contacts({ shuttleBusSchedule = [], parkingLot = [], parkingMap = null, section = null }) {
  const [activeTab, setActiveTab] = useState(0);
  const [parkingPopupOpen, setParkingPopupOpen] = useState(false);

  useEffect(() => {
    if (!parkingPopupOpen) return;
    document.body.style.overflow = "hidden";
    const onKey = (e) => { if (e.key === "Escape") setParkingPopupOpen(false); };
    document.addEventListener("keydown", onKey);
    return () => { document.body.style.overflow = ""; document.removeEventListener("keydown", onKey); };
  }, [parkingPopupOpen]);

  const title    = section?.title    ?? "오시는 길";
  const subtitle = section?.subtitle ?? "밀알교회는 열린 공동체입니다. 주님의 이름으로 언제나 당신을 환영합니다.";

  const shuttleRows = shuttleBusSchedule.length ? shuttleBusSchedule : FALLBACK_SHUTTLE;
  const parkingRows = parkingLot.length ? parkingLot : FALLBACK_PARKING;

  const finchToChurch = useMemo(() => shuttleRows.filter((r) => r.direction === "finch_to_church"), [shuttleRows]);
  const churchToFinch = useMemo(() => shuttleRows.filter((r) => r.direction === "church_to_finch"), [shuttleRows]);

  return (
    <>
    <section className="main-direction" id="contacts">
      <div className="wrap">
        <div className="main-title" data-ani="top">
          <h2 data-heading="5xl" className="main-title__heading">{title}</h2>
          <p className="main-title__sub">{subtitle}</p>
        </div>

        <div className="main-direction__layout" data-grid="2">
          <div className="main-direction__info" data-ani="top">
            <div className="main-direction__tab-row">
              <div className="main-direction__tabs" role="tablist">
                {TABS.map((tab, idx) => (
                  <button
                    key={tab}
                    className={`main-direction__tab${activeTab === idx ? " is-active" : ""}`}
                    role="tab"
                    aria-selected={activeTab === idx}
                    aria-controls={`direction-panel-${idx}`}
                    type="button"
                    onClick={() => setActiveTab(idx)}
                  >
                    {tab}
                  </button>
                ))}
              </div>
            </div>

            {/* 패널 1: 오시는 길 안내 */}
            <div className={`main-direction__panel${activeTab === 0 ? " is-active" : ""}`} id="direction-panel-0" role="tabpanel">
              <div className="main-direction__panel-head">
                <h3 data-heading="xl" className="main-direction__panel-title">오시는 길 안내</h3>
                <ul className="main-direction__info-list">
                  <li className="main-direction__info-item">
                    <img className="main-direction__info-icon" src="/images/main/ic-location01.svg" alt="" />
                    <p data-text="default">405 Gordon Baker Rd.<br />Toronto Ontario Canada M2H 2S6</p>
                  </li>
                  <li className="main-direction__info-item">
                    <img className="main-direction__info-icon" src="/images/main/ic-location02.svg" alt="" />
                    <p data-text="default">416-226-4190</p>
                  </li>
                  <li className="main-direction__info-item">
                    <img className="main-direction__info-icon" src="/images/main/ic-location03.svg" alt="" />
                    <p data-text="default">milalchurch405@gmail.com</p>
                  </li>
                </ul>
              </div>
              <ul className="main-direction__quick-links">
                <li>
                  <a className="main-direction__quick-btn" href="https://maps.app.goo.gl/qG3Ycp82AguX6WjN7" target="_blank" rel="noopener noreferrer">
                    <div className="main-direction__quick-icon main-direction__quick-icon--map"><img src="/images/main/ic-map01.svg" alt="" aria-hidden="true" /></div>
                    <span data-text="xsm-sb">구글맵 보기</span>
                  </a>
                </li>
                <li>
                  <a className="main-direction__quick-btn" href="https://youtube.com/@milalchurch?si=j7abaAQkcDaHUn5Q" target="_blank" rel="noopener noreferrer">
                    <div className="main-direction__quick-icon main-direction__quick-icon--youtube"><img src="/images/main/ic-map02.svg" alt="" aria-hidden="true" /></div>
                    <span data-text="xsm-sb">실시간 예배</span>
                  </a>
                </li>
                <li>
                  <a className="main-direction__quick-btn" href="https://www.instagram.com/milalchurch_toronto/" target="_blank" rel="noopener noreferrer">
                    <div className="main-direction__quick-icon main-direction__quick-icon--instagram"><img src="/images/main/ic-map03.svg" alt="" aria-hidden="true" /></div>
                    <span data-text="xsm-sb">인스타그램</span>
                  </a>
                </li>
                <li>
                  <a className="main-direction__quick-btn" href="https://pf.kakao.com/_xdqzRK" target="_blank" rel="noopener noreferrer">
                    <div className="main-direction__quick-icon main-direction__quick-icon--kakao"><img src="/images/main/ic-map04.svg" alt="" aria-hidden="true" /></div>
                    <span data-text="xsm-sb">카카오톡 채널</span>
                  </a>
                </li>
              </ul>
            </div>

            {/* 패널 2: 셔틀버스 안내 */}
            <div className={`main-direction__panel${activeTab === 1 ? " is-active" : ""}`} id="direction-panel-1" role="tabpanel">
              <div className="main-direction__shuttle">
                <div>
                  <h3 data-heading="xl" className="main-direction__panel-title">셔틀버스 안내</h3>
                  <p className="main-direction__shuttle-desc">
                    밀알교회는 Finch Station에서 교회까지 셔틀버스 운행을 하고 있습니다.<br />
                    Finch 지하철역 <strong>Passenger Pick-Up에서 탑승</strong>하실 수 있습니다.
                  </p>
                </div>
                <ul className="main-direction__shuttle-list">
                  <li className="main-direction__shuttle-route">
                    <strong className="main-direction__shuttle-title">Finch → 교회</strong>
                    <ul className="main-direction__shuttle-times">
                      {finchToChurch.map((row) => (
                        <li key={row.id ?? row.sort_order} className="main-direction__shuttle-time">
                          {row.time} ({row.service_label})
                        </li>
                      ))}
                    </ul>
                  </li>
                  <li className="main-direction__shuttle-route">
                    <strong className="main-direction__shuttle-title">교회 → Finch</strong>
                    <ul className="main-direction__shuttle-times">
                      {churchToFinch.map((row) => (
                        <li key={row.id ?? row.sort_order} className="main-direction__shuttle-time">
                          {row.time} ({row.service_label})
                        </li>
                      ))}
                    </ul>
                  </li>
                </ul>
              </div>
            </div>

            {/* 패널 3: 주차장 안내 */}
            <div className={`main-direction__panel${activeTab === 2 ? " is-active" : ""}`} id="direction-panel-2" role="tabpanel">
              <h3 data-heading="xl" className="main-direction__panel-title">주차장 안내</h3>
              <ul data-list="num">
                {parkingRows.map((row) => (
                  <li key={row.id}>
                    {row.content.split("\n").map((line, i) => (
                      <span key={i}>{line}{i < row.content.split("\n").length - 1 && <br />}</span>
                    ))}
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* 지도 */}
          <div className="main-direction__map" data-ani="top">
            <div className={`main-direction__map-panel${activeTab === 0 ? " is-active" : ""}`} id="dir-map-0">
              <iframe
                title="밀알교회 오시는 길 지도"
                src={MAP_SRC}
                allowFullScreen
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div className={`main-direction__map-panel${activeTab === 1 ? " is-active" : ""}`} id="dir-map-1">
              <iframe
                title="밀알교회 셔틀버스 안내 지도"
                src={SHUTTLE_MAP_SRC}
                allowFullScreen
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div className={`main-direction__map-panel main-direction__map-panel--parking${activeTab === 2 ? " is-active" : ""}`} id="dir-map-2">
              <div
                className="parking-map__wrap"
                role="button"
                tabIndex={0}
                onClick={() => setParkingPopupOpen(true)}
                onKeyDown={(e) => e.key === "Enter" && setParkingPopupOpen(true)}
              >
                <img src={parkingMap?.image_url || "/images/main/parking-map.jpg"} alt={parkingMap?.alt_text || "밀알교회 주차장 안내 지도"} />
                <div className="parking-map__hover-btn">
                  <img src="/images/main/icon-zoom.svg" alt="" aria-hidden="true" />
                  <p>크게 보기</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {parkingPopupOpen && (
      <div className="parking-map-popup" role="dialog" aria-modal="true" aria-label="주차장 안내 지도 크게 보기" onClick={() => setParkingPopupOpen(false)}>
        <button className="parking-map-popup__close" type="button" aria-label="닫기" onClick={() => setParkingPopupOpen(false)}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        <img
          className="parking-map-popup__img"
          src={parkingMap?.image_url || "/images/main/parking-map.jpg"}
          alt={parkingMap?.alt_text || "밀알교회 주차장 안내 지도"}
          onClick={(e) => e.stopPropagation()}
        />
      </div>
    )}
  </>
  );
}
