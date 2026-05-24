import { useEffect, useState } from "react";
import "../css/PdfImagePreviewModal.css";

let sharedPdfWorkerPort = null;

function ensurePdfWorkerPort(pdfjs) {
  if (!sharedPdfWorkerPort) {
    sharedPdfWorkerPort = new Worker(
      new URL("pdfjs-dist/legacy/build/pdf.worker.min.mjs", import.meta.url),
      { type: "module" },
    );
  }

  if (pdfjs.GlobalWorkerOptions.workerPort !== sharedPdfWorkerPort) {
    pdfjs.GlobalWorkerOptions.workerPort = sharedPdfWorkerPort;
  }
}

export default function PdfImagePreviewModal({ open, pdfUrl, onClose }) {
  const [previewImages, setPreviewImages] = useState([]);
  const [isPreviewLoading, setIsPreviewLoading] = useState(false);
  const [previewError, setPreviewError] = useState("");
  const [previewScale, setPreviewScale] = useState(1);

  useEffect(() => {
    if (!open || !pdfUrl) return;

    let cancelled = false;

    async function loadPdfImages() {
      try {
        setIsPreviewLoading(true);
        setPreviewError("");

        const pdfjs = await import("pdfjs-dist/legacy/build/pdf");
        ensurePdfWorkerPort(pdfjs);

        const loadingTask = pdfjs.getDocument(pdfUrl);
        const pdf = await loadingTask.promise;

        const pages = [];
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum += 1) {
          const page = await pdf.getPage(pageNum);
          const viewport = page.getViewport({ scale: 1.4 });
          const canvas = document.createElement("canvas");
          const context = canvas.getContext("2d");

          if (!context) {
            throw new Error("Canvas context를 생성할 수 없습니다.");
          }

          canvas.width = viewport.width;
          canvas.height = viewport.height;

          await page.render({ canvasContext: context, viewport }).promise;
          pages.push(canvas.toDataURL("image/png"));
        }

        if (!cancelled) {
          setPreviewImages(pages);
        }
      } catch (_error) {
        if (!cancelled) {
          setPreviewError("미리보기를 불러오지 못했습니다.");
        }
      } finally {
        if (!cancelled) {
          setIsPreviewLoading(false);
        }
      }
    }

    loadPdfImages();

    return () => {
      cancelled = true;
    };
  }, [open, pdfUrl]);

  useEffect(() => {
    if (open) {
      setPreviewScale(1);
    }
  }, [open]);

  useEffect(() => {
    if (!open) return;
    const onKeyDown = (event) => {
      if (event.key === "Escape") {
        onClose();
      }
    };

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", onKeyDown);

    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [open, onClose]);

  const handleDownload = async () => {
    if (!pdfUrl) return;

    try {
      const response = await fetch(pdfUrl);
      if (!response.ok) {
        throw new Error("Failed to download file");
      }

      const blob = await response.blob();
      const objectUrl = URL.createObjectURL(blob);
      const a = document.createElement("a");
      const urlPath = String(pdfUrl).split("?")[0].split("#")[0];
      const fileName = decodeURIComponent(urlPath.substring(urlPath.lastIndexOf("/") + 1)) || "notice.pdf";

      a.href = objectUrl;
      a.download = fileName.toLowerCase().endsWith(".pdf") ? fileName : `${fileName}.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(objectUrl);
    } catch (_error) {
      setPreviewError("PDF 다운로드에 실패했습니다.");
    }
  };

  if (!open) {
    return null;
  }

  return (
    <div
      className="pdf-preview-modal"
      role="dialog"
      aria-modal="true"
      aria-label="PDF 미리보기"
      onClick={onClose}
    >
      <div className="pdf-preview-modal__inner" onClick={(e) => e.stopPropagation()}>
        {isPreviewLoading && <p className="pdf-preview-modal__state">미리보기를 불러오는 중입니다...</p>}
        {!isPreviewLoading && previewError && (
          <p className="pdf-preview-modal__state">{previewError}</p>
        )}
        {!isPreviewLoading && !previewError && (
          <div
            className="pdf-preview-modal__pages"
            style={{ transform: `scale(${previewScale})` }}
          >
            {previewImages.map((src, idx) => (
              <img key={idx} src={src} alt={`PDF 페이지 ${idx + 1}`} />
            ))}
          </div>
        )}

        <div className="pdf-preview-modal__fab-group" aria-label="PDF controls">
          <button
            type="button"
            className="pdf-preview-modal__fab"
            aria-label="확대"
            onClick={() => setPreviewScale((prev) => Math.min(2.5, +(prev + 0.15).toFixed(2)))}
          >
            +
          </button>
          <button
            type="button"
            className="pdf-preview-modal__fab"
            aria-label="축소"
            onClick={() => setPreviewScale((prev) => Math.max(0.6, +(prev - 0.15).toFixed(2)))}
          >
            -
          </button>
          <button
            type="button"
            className="pdf-preview-modal__fab pdf-preview-modal__fab--download"
            aria-label="다운로드"
            onClick={handleDownload}
          >
            <i className="pdf-preview-modal__fab-download-icon" aria-hidden="true" />
          </button>
          <button
            type="button"
            className="pdf-preview-modal__fab pdf-preview-modal__fab--close"
            aria-label="닫기"
            onClick={onClose}
          >
            x
          </button>
        </div>
      </div>
    </div>
  );
}
