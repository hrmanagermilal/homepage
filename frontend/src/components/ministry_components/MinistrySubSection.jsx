import { useMemo, useState } from "react";
import RemoveRedEyeIcon from "@mui/icons-material/RemoveRedEye";
import PdfImagePreviewModal from "../common/PdfImagePreviewModal";
import "./css/MinistrySubSection.css";

export default function MinistrySubSection({
  description,
  points = [],
  noticeTitle = "4월 순모임 교재 공유드립니다.",
  noticeDescription = "PDF파일을 다운 받으셔서 순모임에 활용하세요.",
  noticeButtonLabel = "PDF 다운로드",
  noticeButtonHref = "#",
  noticeButtonExternal = false,
  ctaLabel,
  ctaHref = "#",
  ctaExternal = false,
}) {
  const [isPreviewOpen, setIsPreviewOpen] = useState(false);

  const isPdfNotice = useMemo(() => {
    if (!noticeButtonHref) return false;
    const normalized = String(noticeButtonHref).trim().toLowerCase();
    const withoutQuery = normalized.split("?")[0].split("#")[0];
    return withoutQuery.endsWith(".pdf");
  }, [noticeButtonHref]);

  return (
    <section className="ministry-subsection">
      <div className="wrap-narrow">
        <div className="ministry-subsection__content">
          <p className="ministry-subsection__desc">{description}</p>
          {points.length > 0 ? (
            <ul className="ministry-subsection__list">
              {points.map((point) => (
                <li key={point} className="ministry-subsection__list-item">
                  {point}
                </li>
              ))}
            </ul>
          ) : null}
        </div>

        {ctaLabel ? (
          <a
            className="btn-basic-big btn-basic-big--trans ministry-subsection__cta"
            href={ctaHref}
            {...(ctaExternal ? { target: "_blank", rel: "noopener noreferrer" } : {})}
          >
            <span>{ctaLabel}</span>
          </a>
        ) : null}

        <div className="ministry-subsection__notice">
          <div className="ministry-subsection__notice-text">
            <p className="ministry-subsection__notice-title">{noticeTitle}</p>
            <p className="ministry-subsection__notice-desc">{noticeDescription}</p>
          </div>
          {isPdfNotice ? (
            <button
              type="button"
              className="btn-basic-big btn-basic-big--trans ministry-subsection__notice-btn ministry-subsection__notice-btn--view"
              onClick={() => setIsPreviewOpen(true)}
            >
              <RemoveRedEyeIcon className="ministry-subsection__notice-view-icon" aria-hidden="true" />
              <span>바로보기</span>
            </button>
          ) : null}
          <a
            className="btn-basic-big btn-basic-big--trans ministry-subsection__notice-btn"
            href={noticeButtonHref}
            target={isPdfNotice ? undefined : noticeButtonExternal ? "_blank" : undefined}
            rel={isPdfNotice ? undefined : noticeButtonExternal ? "noopener noreferrer" : undefined}
            download={isPdfNotice ? "" : undefined}
          >
            <i aria-hidden="true" />
            <span>{noticeButtonLabel}</span>
          </a>
        </div>
      </div>
      <PdfImagePreviewModal
        open={isPreviewOpen && isPdfNotice}
        pdfUrl={noticeButtonHref}
        onClose={() => setIsPreviewOpen(false)}
      />
    </section>
  );
}
