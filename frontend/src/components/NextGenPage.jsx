import NextGenDepartment from "./nextgen_components/NextGenDepartment";
import "./css/SubPage.css";
import "./css/NextGenPage.css";

const NEXTGEN_LNB_ITEMS = [
  { label: "청년부", href: "/nextgen/young-adults" },
  { label: "KM 청소년부", href: "/nextgen/km-youth" },
  { label: "EM 청소년부", href: "/nextgen/em-youth" },
  { label: "아동부", href: "/nextgen/children" },
  { label: "유치부", href: "/nextgen/kindergarten" },
  { label: "유아부", href: "/nextgen/preschool" },
  { label: "영아부", href: "/nextgen/infants" },
];

const DEPARTMENT_CONTENT = {
  "청년부": {
    headingTitle: (
      <>
        Milight, Time to Shine. 하나님이여 우리를 돌이키시고
        <br />
        주의 얼굴빛을 비추사 우리가 구원을 얻게 하소서 (시편 80:3)
      </>
    ),
    headingSub: (
      <>
        토론토의 새벽이슬 같은 청년들이 모이면 예배하고,
        <br />
        흩어지면 빛을 발하는 공동체입니다.
      </>
    ),
    worshipTime: "주일 오후 2시",
    worshipLocation: "밀알교회 1층 본당",
    pastorName: "신효성 목사",
    pastorEmail: "rev.shin@milalchurch.com",
    pastorPhoto: "/images/sub/02-next-generation/pastor-photo.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "청년부 카카오톡 채널 추가하기",
    photoAlt: "신효성 목사 사진",
    noticeTitle: "청년부 소식",
    noticeDescription: "청년부의 소식과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
  },
  "KM 청소년부": {
    headingTitle: "KM 청소년부, 믿음 안에서 함께 성장합니다.",
    headingSub: "말씀과 기도로 다음세대가 정체성을 세우고, 건강한 공동체를 경험하도록 돕습니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 2층 청소년부 예배실",
    pastorName: "차승현 목사",
    pastorEmail: "nextgen@milalchurch.com",
    pastorPhoto: "/images/sub/01-introduction/minister-05.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "KM 청소년부 카카오톡 채널 추가하기",
    photoAlt: "KM 청소년부 담당 교역자",
    noticeTitle: "KM 청소년부 소식",
    noticeDescription: "주간 프로그램과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
  },
  "EM 청소년부": {
    headingTitle: "EM Youth, Grounded in the Word.",
    headingSub: "We gather for worship and discipleship, and go out as Christ-centered witnesses in daily life.",
    worshipTime: "주일 오후 1시",
    worshipLocation: "밀알교회 2층 청소년부 예배실",
    pastorName: "조나단 목사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "EM Youth 소식",
    noticeDescription: "프로그램 일정과 공지사항을 다운로드하세요.",
    noticeButtonLabel: "공지사항 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-09.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "EM Youth 카카오톡 채널 추가하기",
    photoAlt: "EM Youth 담당 교역자",
  },
  "아동부": {
    headingTitle: "아동부, 예수님을 닮아가는 어린이들",
    headingSub: "예배와 말씀, 활동을 통해 아이들이 즐겁게 하나님을 알아가도록 세웁니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 아동부실",
    pastorName: "김진아 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "아동부 프로그램",
    noticeDescription: "월간 프로그램과 학부모 안내자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-13.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "아동부 카카오톡 채널 추가하기",
    photoAlt: "아동부 담당 교역자",
  },
  "유치부": {
    headingTitle: "유치부, 믿음의 씨앗을 심는 시간",
    headingSub: "아이들의 눈높이에 맞춘 예배와 활동으로 하나님의 사랑을 자연스럽게 배우게 합니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 유치부실",
    pastorName: "김비치 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "유치부 프로그램",
    noticeDescription: "월간 공지사항과 부모교육 자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-12.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "유치부 카카오톡 채널 추가하기",
    photoAlt: "유치부 담당 교역자",
  },
  "유아부": {
    headingTitle: "유아부, 사랑 안에서 첫 걸음을",
    headingSub: "부모와 교사가 함께 아이들의 신앙 첫 걸음을 따뜻하게 동행합니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 유아부실",
    pastorName: "주은지 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    noticeTitle: "유아부 프로그램",
    noticeDescription: "월간 프로그램과 양육 안내자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
    pastorPhoto: "/images/sub/01-introduction/minister-13.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "유아부 카카오톡 채널 추가하기",
    photoAlt: "유아부 담당 교역자",
  },
  "영아부": {
    headingTitle: "영아부, 가정과 함께 드리는 예배",
    headingSub: "가장 어린 다음세대가 예배의 기쁨을 경험하도록 부모와 교회가 함께 섬깁니다.",
    worshipTime: "주일 오전 11시",
    worshipLocation: "밀알교회 영아부실",
    pastorName: "주은지 전도사",
    pastorEmail: "nextgen@milalchurch.com",
    pastorPhoto: "/images/sub/01-introduction/minister-13.jpg",
    kakaoLink: "https://pf.kakao.com/_xdqzRK",
    kakaoLabel: "영아부 카카오톡 채널 추가하기",
    photoAlt: "영아부 담당 교역자",
    noticeTitle: "영아부 프로그램",
    noticeDescription: "월간 프로그램과 부모 양육 안내자료를 다운로드하세요.",
    noticeButtonLabel: "자료 다운로드",
    noticeButtonHref: "#",
  },
};


function SubVisual({ title }) {
  return (
    <section className="sub-visual" aria-label="다음세대 서브 비주얼">
      <div className="sub-visual__bg" aria-hidden="true">
        <figure className="sub-visual__bg-img nextgen-bg" />
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
              <path d="M3.46447 1.46447L7 5L3.46447 8.53553" stroke="white" strokeOpacity="0.2" strokeWidth="2" strokeLinecap="round" />
            </svg>
          </span>
          <span className="sub-visual__lnb-text">다음세대</span>
        </nav>
        <h2 className="sub-visual__title">{title}</h2>
      </div>
      <div className="sub-visual__scroll-down" aria-hidden="true">
        <i />
        <span>SCROLL DOWN</span>
      </div>
    </section>
  );
}

function SubLnb({ currentTitle }) {
  return (
    <div className="lnb-wrap">
      <nav className="lnb" aria-label="다음세대 메뉴">
        {NEXTGEN_LNB_ITEMS.map((item, idx) => (
          <a
            key={item.label}
            className={`lnb__btn${item.label === currentTitle ? " is-active" : ""}${idx > 0 ? " lnb__btn--sep" : ""}`}
            href={item.href}
          >
            {item.label}
          </a>
        ))}
      </nav>
    </div>
  );
}

export default function NextGenPage({ title }) {
  const safeTitle = DEPARTMENT_CONTENT[title] ? title : "청년부";
  const content = DEPARTMENT_CONTENT[safeTitle];

  return (
    <>
      <SubVisual title={safeTitle} />
      <div className="sub-content" id="content">
        <SubLnb currentTitle={safeTitle} />
        <NextGenDepartment {...content} />
      </div>
    </>
  );
}
