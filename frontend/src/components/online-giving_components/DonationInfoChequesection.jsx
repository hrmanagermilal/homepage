export default function DonationInfoChequeSection() {
  return (
    <div className="donation-info__col">
      <h4 className="donation-info__title">수표 (우편 전송)</h4>
      <div>
        <p className="donation-info__payable-main">Payable to Milal Church</p>
        <address className="donation-info__payable-addr">
          Milal Church – Finance
          <br />
          405 Gordon Baker Rd. North York, ON M2H 2S6
        </address>
      </div>
    </div>
  );
}
