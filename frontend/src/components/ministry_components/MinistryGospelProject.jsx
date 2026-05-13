import MinistrySubSection from "./MinistrySubSection";

export default function MinistryGospelProject() {
  return (
    <section id="ministry08">
      <MinistrySubSection
        subtitle="가스펠프로젝트"
        title="교회같은 가정을 세우는 복음교육"
        image="/images/sub/visual/gospel-intro.jpg"
        description="가스펠프로젝트는 성경의 큰 흐름 안에서 복음을 배우고, 가정과 교회가 같은 방향으로 다음세대를 세워가도록 돕는 통합 양육 과정입니다."
        points={[
          "전 세대가 같은 본문으로 배우는 커리큘럼",
          "가정예배와 연계되는 주간 적용 가이드",
          "교사/부모 훈련을 통한 지속 가능한 신앙교육",
        ]}
        noticeTitle="가스펠프로젝트 안내 자료"
        noticeDescription="학기 일정과 교재 안내, 가정 적용 자료를 확인하세요."
        noticeButtonLabel="안내 자료 다운로드"
        noticeButtonHref="#"
      />
    </section>
  );
}
