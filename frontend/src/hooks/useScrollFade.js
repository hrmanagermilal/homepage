import { useEffect } from "react";
import { FEATURES } from "../config/features";

/**
 * useScrollFade
 *
 * Replicates the publish/js/motion.js scroll animation system.
 * Elements with a `data-ani` attribute start invisible and slide/fade
 * in once they scroll into view. Supported values:
 *
 *   top | bottom | left | right | rotate | scale | img | preserveTop | hidden | skew
 *
 * Controlled by FEATURES.SCROLL_FADE (default: true).
 * Uses MutationObserver to automatically handle SPA route changes.
 */
export function useScrollFade() {
  useEffect(() => {
    if (!FEATURES.SCROLL_FADE) return;

    let items = [];
    let rafId = null;

    // Wrap "skew" and "hidden" elements in an overflow-hidden span
    // so the translateY doesn't bleed outside the parent (same as publish/js/motion.js)
    function maybeWrap(item) {
      const type = item.getAttribute("data-ani");
      if (type !== "skew" && type !== "hidden") return;
      if (!item.parentElement) return;
      if (item.parentElement.hasAttribute("data-ani-wrap")) return;

      const wrapper = document.createElement("span");
      wrapper.setAttribute("data-ani-wrap", "");
      item.parentElement.insertBefore(wrapper, item);
      wrapper.appendChild(item);
    }

    function checkVisibility() {
      const scrollBottom = window.scrollY + window.innerHeight;
      const isMobile = window.innerWidth < 540;

      items.forEach((item) => {
        if (item.classList.contains("is_moved")) return;
        const posTrigger = isMobile ? 0 : item.offsetHeight / 3;
        const rect = item.getBoundingClientRect();
        if (scrollBottom > window.scrollY + rect.top + posTrigger) {
          item.classList.add("is_moved");
        }
      });
    }

    function refreshItems() {
      // Disconnect before DOM manipulation to prevent our own changes
      // (insertBefore / appendChild during wrapping) from re-triggering the observer
      observer.disconnect();

      Array.from(document.querySelectorAll("[data-ani]")).forEach(maybeWrap);
      items = Array.from(document.querySelectorAll("[data-ani]"));
      checkVisibility();

      // Reconnect to watch for future route/content changes
      observer.observe(document.body, { childList: true, subtree: true });
    }

    // Re-scan whenever React adds/removes nodes (SPA route changes, data loads)
    const observer = new MutationObserver(() => {
      if (rafId) cancelAnimationFrame(rafId);
      rafId = window.requestAnimationFrame(refreshItems);
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Initial run
    refreshItems();
    window.addEventListener("scroll", checkVisibility, { passive: true });

    return () => {
      observer.disconnect();
      if (rafId) cancelAnimationFrame(rafId);
      window.removeEventListener("scroll", checkVisibility);
    };
  }, []);
}

export default useScrollFade;
