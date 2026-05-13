export default function DonationNoticeModal({ isOpen, onClose }) {
  return (
    <div
      className={`donation-modal${isOpen ? " is-open" : ""}`}
      id="donationNoticeModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="donationNoticeTitle"
      aria-hidden={!isOpen}
      onClick={(e) => {
        if (e.target === e.currentTarget) {
          onClose();
        }
      }}
    >
      <div className="donation-modal__box">
        <button
          className="donation-modal__close"
          type="button"
          aria-label="유의사항 닫기"
          onClick={onClose}
        />
        <span className="donation-modal__icon" aria-hidden="true" />
        <h4 className="donation-modal__title" id="donationNoticeTitle">
          신용카드로 헌금, 유의사항 안내
        </h4>
        <p className="donation-modal__desc">
          참고로 헌금시 Zeffy Canada에 후원 도네이션이 있습니다.
          <br />
          [Other] 를 선택하시고 $0 Contribution을 입력하시면
          <br />
          수수료 없이 밀알교회에 헌금 됩니다.
        </p>
        <a
          className="btn-basic btn-basic--white donation-modal__btn"
          href="https://www.zeffy.com/en-CA/donation-form/f833c7ac-1b7f-4703-83e1-e25d51579cc5"
          target="_blank"
          rel="noopener noreferrer"
        >
          신용카드로 헌금 드리기
        </a>
      </div>
    </div>
  );
}
