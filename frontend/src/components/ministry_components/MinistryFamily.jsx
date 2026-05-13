import MinistrySubSection from "./MinistrySubSection";

export default function MinistryFamily() {
  return (
    <section id="ministry03">
      <MinistrySubSection
        subtitle="가정"
        title="당신의 첫 제자는 당신의 자녀입니다."
        image="/images/sub/03-ministry/sub-visual-bg.jpg"
        description="가정 사역은 부부와 부모, 자녀가 함께 하나님 안에서 건강한 관계를 세워가도록 돕습니다."
        points={[
          "가정예배와 신앙 대화 훈련",
          "부부/부모 교육 및 상담 연계",
          "세대 간 신앙 계승을 위한 실천 안내",
        ]}
        noticeTitle="가정예배 자료를 안내드립니다."
        noticeDescription="가정에서 바로 활용할 수 있는 월간 예배 가이드를 내려받으세요."
        noticeButtonLabel="가정예배 자료 다운로드"
        noticeButtonHref="#"
      />
    </section>
  );
}
