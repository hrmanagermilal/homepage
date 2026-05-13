import MinistrySubSection from "./MinistrySubSection";

export default function MinistryLoveToronto() {
  return (
    <section id="ministry07">
      <MinistrySubSection
        subtitle="러브토론토"
        title="도시를 사랑으로 섬기는 사역"
        image="/images/sub/03-ministry/sub-visual-bg-front.jpg"
        description="러브토론토 사역은 도시 속 이웃을 실제적으로 섬기며 복음을 전하는 선교적 실천입니다."
        points={[
          "노숙인/취약계층 지원 사역 참여",
          "지역 커뮤니티 연계 봉사",
          "기도와 후원, 현장 참여의 동역",
        ]}
        noticeTitle="러브토론토 봉사 일정 안내"
        noticeDescription="다음 봉사 일정과 참여 방법을 안내문에서 확인하세요."
        noticeButtonLabel="봉사 안내 보기"
        noticeButtonHref="https://lovetoronto.org/"
        noticeButtonExternal
        ctaLabel="러브토론토 홈페이지"
        ctaHref="https://lovetoronto.org/"
        ctaExternal
      />
    </section>
  );
}
