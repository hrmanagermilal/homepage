import MinistrySubSection from "./MinistrySubSection";

export default function MinistryScholarship() {
  return (
    <section id="ministry05">
      <MinistrySubSection
        subtitle="장학"
        title="다음세대를 세우는 믿음의 투자"
        image="/images/sub/03-ministry/ministry-yanguk-bg.jpg"
        description="장학 사역은 신앙과 학업의 균형 속에서 다음세대가 하나님이 주신 달란트를 발견하고 성장하도록 지원합니다."
        points={[
          "장학 대상 발굴 및 멘토링 연계",
          "학업과 진로를 위한 기도 후원",
          "교회 내외 장학 기금 운영",
        ]}
        noticeTitle="장학 신청 안내문을 확인하세요."
        noticeDescription="장학 일정, 대상, 제출서류를 담은 안내 PDF를 제공합니다."
        noticeButtonLabel="장학 안내 다운로드"
        noticeButtonHref="#"
      />
    </section>
  );
}
