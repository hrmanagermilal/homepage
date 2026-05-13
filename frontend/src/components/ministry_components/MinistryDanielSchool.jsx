import MinistrySubSection from "./MinistrySubSection";

export default function MinistryDanielSchool() {
  return (
    <section id="ministry06">
      <MinistrySubSection
        subtitle="다니엘한글문화학교"
        title="언어와 문화를 잇는 배움의 자리"
        image="/images/sub/03-ministry/sub-visual-bg.jpg"
        description="다니엘한글문화학교는 다음세대가 한글과 한국 문화를 배우며 신앙 안에서 정체성을 세워가도록 돕습니다."
        points={[
          "연령별 한글/문화 통합 교육",
          "가정과 연계한 학습 지원",
          "믿음 안에서 건강한 정체성 형성",
        ]}
        noticeTitle="다니엘한글문화학교 등록 안내"
        noticeDescription="학기 일정과 등록 방법, 준비사항을 PDF에서 확인해 주세요."
        noticeButtonLabel="등록 안내 다운로드"
        noticeButtonHref="#"
        ctaLabel="다니엘한글문화학교 바로가기"
        ctaHref="#"
      />
    </section>
  );
}
