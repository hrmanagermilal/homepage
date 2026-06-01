import { useEffect, useRef } from "react";
import { FEATURES } from "../config/features";

import Hero from "./landing_components/Hero";
import Sermon from "./landing_components/Sermon";
import ServiceTime from "./landing_components/ServiceTime";
import Jubo from "./landing_components/Jubo";
import Announcement from "./landing_components/Announcement";
import Contacts from "./landing_components/Contacts";
import FooterTop from "./FooterTop";

export default function LandingPage({
  hero,
  quickLinks,
  sermons,
  departments,
  serviceTimes,
  latestBulletin,
  notices,
  shuttleBusSchedule,
  parkingLot,
  parkingMap,
  bannerImage,
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

    const getActiveSectionIndex = () => {
      let activeIndex = 0;
      sections.forEach((section, index) => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= 4) {
          activeIndex = index;
        }
      });
      return activeIndex;
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

      const direction = event.deltaY > 0 ? 1 : -1;
      const currentIndex = getActiveSectionIndex();
      const currentSection = sections[currentIndex];
      const rect = currentSection.getBoundingClientRect();

      // Scrolling down but section bottom is not yet fully visible → scroll one viewport down (or to section bottom if closer)
      if (direction > 0 && rect.bottom > window.innerHeight + 4) {
        event.preventDefault();
        wheelLockUntil = now + 500;
        isAnimating = true;
        const scrollAmount = Math.min(window.innerHeight, rect.bottom - window.innerHeight);
        window.scrollTo({ top: window.scrollY + scrollAmount, behavior: "smooth" });
        window.setTimeout(() => { isAnimating = false; }, 700);
        return;
      }

      // Scrolling up but section top is not yet fully visible → scroll one viewport up (or to section top if closer)
      if (direction < 0 && rect.top < -4) {
        event.preventDefault();
        wheelLockUntil = now + 500;
        isAnimating = true;
        const scrollAmount = Math.min(window.innerHeight, -rect.top);
        window.scrollTo({ top: window.scrollY - scrollAmount, behavior: "smooth" });
        window.setTimeout(() => { isAnimating = false; }, 700);
        return;
      }

      const nextIndex = Math.max(0, Math.min(sections.length - 1, currentIndex + direction));

      if (nextIndex === currentIndex) {
        return;
      }

      event.preventDefault();
      wheelLockUntil = now + 500;
      moveToSection(nextIndex);
    };

    if (FEATURES.SCROLL_SNAP_ENABLED) {
      window.addEventListener("wheel", onWheel, { passive: false });
      return () => {
        window.removeEventListener("wheel", onWheel);
      };
    }
    return () => {};
  }, []);

  return (
    <div ref={containerRef}>
      <div data-snap-section="true">
        <Hero hero={hero} quickLinks={quickLinks} />
      </div>
      <div data-snap-section="true">
        <Sermon items={sermons} section={sections.find((s) => s.category === "Sermon")} />
      </div>
      <div data-snap-section="true">
        <Jubo items={latestBulletin ? [latestBulletin] : []} section={sections.find((s) => s.category === "Jubo")} />
      </div>
      <div data-snap-section="true">
        <ServiceTime serviceTimes={serviceTimes} section={sections.find((s) => s.category === "Worship")} />
      </div>
      <div data-snap-section="true">
        <Announcement items={notices} section={sections.find((s) => s.category === "News")} />
      </div>
      <div data-snap-section="true">
        <Contacts shuttleBusSchedule={shuttleBusSchedule} parkingLot={parkingLot} parkingMap={parkingMap} section={sections.find((s) => s.category === "Directions")} />
      </div>
      <div data-snap-section="true">
        <FooterTop bannerImage={bannerImage} section={sections.find((s) => s.category === "Community")} />
      </div>
    </div>
  );
}
