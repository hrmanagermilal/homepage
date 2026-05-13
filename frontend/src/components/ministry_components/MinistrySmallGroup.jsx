import MinistrySubSection from "./MinistrySubSection";

export default function MinistrySmallGroup() {
  return (
    <section id="ministry02">
      <MinistrySubSection
        subtitle="소그룹"
        title="함께 말씀으로 자라는 공동체"
        image="/images/sub/03-ministry/ministry-yanguk-bg.jpg"
        description="소그룹은 예배의 감동을 일상의 나눔으로 이어가는 자리입니다. 연령과 삶의 단계에 맞는 모임을 통해 말씀을 적용하고 서로를 돌봅니다."
        points={[
          "주중 정기 모임으로 말씀 나눔과 기도",
          "새가족이 자연스럽게 정착하도록 연결",
          "필요를 함께 나누고 실제적으로 돕는 공동체",
        ]}
        noticeTitle="소그룹 나눔 가이드 공유드립니다."
        noticeDescription="소그룹 인도자와 순원이 함께 사용할 수 있는 PDF 자료입니다."
        noticeButtonLabel="소그룹 자료 다운로드"
        noticeButtonHref="#"
      />
    </section>
  );
}
