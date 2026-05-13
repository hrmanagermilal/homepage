export default function DonationNotice() {
  return (
    <div className="donation-notice" role="note">
      <span className="donation-notice__icon" aria-hidden="true">
        !
      </span>
      <p className="donation-notice__text">
        CRA 규정상 헌금 영수증은 기부자의 영문 이름과 일치하도록 하시고{" "}
        <strong>헌금 봉투 번호</strong>를 꼭 기입해주세요.
      </p>
    </div>
  );
}
