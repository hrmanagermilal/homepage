export default function DonationInfoBankSection() {
  const accountDetails = [
    { label: "Name", value: "Milal Church" },
    { label: "Address", value: "405 Gordon Baker Rd. North York, ON CANADA M2H 2S6" },
    { label: "Transit NO.", value: "08048" },
    { label: "Institution No.", value: "355" },
    { label: "Account No.", value: "702-000-167166" },
    { label: "Swift Code", value: "SHBKCATTXXX" },
  ];

  return (
    <div className="donation-info__col donation-info__col--right">
      <h4 className="donation-info__title">계좌로 송금</h4>
      <div className="donation-info__bank-box">
        <img src="/images/sub/05-online-donation/shinhan-logo.svg" alt="신한은행" />
      </div>
      <dl className="donation-info__account">
        {accountDetails.map((detail, idx) => (
          <div key={idx} className="donation-info__account-row">
            <dt>{detail.label}</dt>
            <span className="donation-info__account-sep" aria-hidden="true" />
            <dd>{detail.value}</dd>
          </div>
        ))}
      </dl>
    </div>
  );
}
