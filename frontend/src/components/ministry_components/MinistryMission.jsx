import MinistrySubSection from "./MinistrySubSection";

export default function MinistryMission() {
  return (
    <section id="ministry04">
      <MinistrySubSection
        subtitle="선교"
        title="복음을 들고 세상으로"
        image="/images/sub/03-ministry/sub-visual-bg-front.jpg"
        description="지역과 열방을 향한 선교적 삶을 실천합니다. 교회는 선교사와 선교지를 위해 지속적으로 기도하고 동역합니다."
        points={[
          "선교지 후원과 정기 중보기도",
          "지역사회 섬김 프로젝트 참여",
          "단기 선교와 다음세대 선교 교육",
        ]}
        noticeTitle="선교 기도제목과 소식지를 나눕니다."
        noticeDescription="이번 달 선교 소식과 중보기도 제목을 PDF로 확인하세요."
        noticeButtonLabel="선교 소식지 다운로드"
        noticeButtonHref="#"
      />
    </section>
  );
}
