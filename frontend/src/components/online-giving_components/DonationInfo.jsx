import DonationInfoChequeSection from "./DonationInfoChequesection";
import DonationInfoBankSection from "./DonationInfoBankSection";

export default function DonationInfo() {
  return (
    <div className="donation-info">
      <DonationInfoChequeSection />
      <DonationInfoBankSection />
    </div>
  );
}
