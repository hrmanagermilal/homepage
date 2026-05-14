import { useState } from "react";
import "./css/SubPage.css";
import "./css/OnlineGivingPage.css";
import DonationSubVisual from "./online-giving_components/DonationSubVisual";
import DonationNotice from "./online-giving_components/DonationNotice";
import DonationCard from "./online-giving_components/DonationCard";
import DonationInfo from "./online-giving_components/DonationInfo";
import DonationNoticeModal from "./online-giving_components/DonationNoticeModal";

const OFFERING_EMAIL = "offering.milalchurch@gmail.com";

export default function OnlineGivingPage() {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const handleCopyEmail = async () => {
    try {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(OFFERING_EMAIL);
      } else {
        // Fallback for non-secure contexts
        const textarea = document.createElement("textarea");
        textarea.value = OFFERING_EMAIL;
        textarea.style.position = "fixed";
        textarea.style.left = "-9999px";
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        document.execCommand("copy");
        textarea.remove();
      }
      alert("주소가 클립보드에 복사되었습니다");
    } catch (err) {
      console.error("Failed to copy email:", err);
      alert("주소 복사에 실패했습니다");
    }
  };

  const eTransferButtons = [{ label: "이메일 주소 복사하기", onClick: handleCopyEmail }];

  const creditCardButtons = [
    { label: "이용시 유의사항", onClick: () => setIsModalOpen(true) },
    {
      label: "헌금 드리기",
      href: "https://www.zeffy.com/en-CA/donation-form/f833c7ac-1b7f-4703-83e1-e25d51579cc5",
    },
  ];

  return (
    <>
      <DonationSubVisual />
      <div className="sub-content" id="content">
        <section className="donation">
          <div className="wrap-narrow">
            <h3 className="donation-heading" data-heading="5xl" data-ani="top">
              온라인 헌금
            </h3>

            <DonationNotice />

            <div className="donation-cards">
              <DonationCard
                logo="/images/sub/05-online-donation/interac-logo.svg"
                logoAlt="Interac"
                title="E-Transfer로 헌금 드리기"
                description={`주소: ${OFFERING_EMAIL}<br />이메일로 송금해주시기 바랍니다.`}
                buttons={eTransferButtons}
              />

              <DonationCard
                logo="/images/sub/05-online-donation/credit-card.svg"
                logoAlt=""
                logoWide={true}
                title="신용카드로 헌금 드리기"
                description="Zeffy Canada 를 통해 수수료 없이<br />신용카드로 헌금 하실수 있습니다."
                buttons={creditCardButtons}
              />
            </div>

            <DonationInfo />
          </div>
        </section>
      </div>

      <DonationNoticeModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} />
    </>
  );
}
