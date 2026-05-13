import { useEffect, useRef } from "react";

import Hero from "./landing_components/Hero";
import Sermon from "./landing_components/Sermon";
import ServiceTime from "./landing_components/ServiceTime";
import Jubo from "./landing_components/Jubo";
import Announcement from "./landing_components/Announcement";
import Contacts from "./Contacts";
import FooterTop from "./FooterTop";

export default function LandingPage({
  hero,
  quickLinks,
  sermons,
  departments,
  bulletins,
  announcements,
  sections,
  togetherItems,
}) {
  const containerRef = useRef(null);

  useEffect(() => {
    const container = containerRef.current;
    if (!container) {
      return;
    }

    const isDesktopScrollSnap =
      typeof window !== "undefined" &&
      window.matchMedia("(min-width: 1024px)").matches &&
      window.matchMedia("(pointer: fine)").matches;

    if (!isDesktopScrollSnap) {
      return;
    }

    const sections = Array.from(container.querySelectorAll("[data-snap-section='true']"));
    if (!sections.length) {
      return;
    }

    let isAnimating = false;
    let wheelLockUntil = 0;

    const getClosestSectionIndex = () => {
      const viewportMid = window.innerHeight / 2;
      let bestIndex = 0;
      let bestDistance = Number.POSITIVE_INFINITY;

      sections.forEach((section, index) => {
        const rect = section.getBoundingClientRect();
        const sectionMid = rect.top + rect.height / 2;
        const distance = Math.abs(sectionMid - viewportMid);
        if (distance < bestDistance) {
          bestDistance = distance;
          bestIndex = index;
        }
      });

      return bestIndex;
    };

    const moveToSection = (index) => {
      if (index < 0 || index >= sections.length) {
        return;
      }

      isAnimating = true;
      sections[index].scrollIntoView({ behavior: "smooth", block: "start" });
      window.setTimeout(() => {
        isAnimating = false;
      }, 700);
    };

    const onWheel = (event) => {
      const now = Date.now();
      if (isAnimating || now < wheelLockUntil) {
        event.preventDefault();
        return;
      }

      if (Math.abs(event.deltaY) < 8) {
        return;
      }

      const currentIndex = getClosestSectionIndex();
      const direction = event.deltaY > 0 ? 1 : -1;
      const nextIndex = Math.max(0, Math.min(sections.length - 1, currentIndex + direction));

      if (nextIndex === currentIndex) {
        return;
      }

      event.preventDefault();
      wheelLockUntil = now + 500;
      moveToSection(nextIndex);
    };

    window.addEventListener("wheel", onWheel, { passive: false });
    return () => {
      window.removeEventListener("wheel", onWheel);
    };
  }, []);

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <Hero hero={hero} quickLinks={quickLinks} />
      </div>
      <div data-snap-section="true">
        <Sermon items={sermons} section={sections.find((s) => s.title === "최신 설교")} />
      </div>
      <div data-snap-section="true">
        <Jubo items={bulletins} section={sections.find((s) => s.title === "주보")} />
      </div>
      <div data-snap-section="true">
        <ServiceTime departments={departments} section={sections.find((s) => s.title === "예배 시간")} />
      </div>
      <div data-snap-section="true">
        <Announcement items={announcements} section={sections.find((s) => s.title === "공지사항")} />
      </div>
      <div data-snap-section="true">
        <Contacts section={sections.find((s) => s.title === "오시는 길")} />
      </div>
      <div data-snap-section="true">
        <FooterTop items={togetherItems} section={sections.find((s) => s.title === "함께하는 교회")} />
      </div>
    </div>
  );
}
