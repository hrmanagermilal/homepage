import { useCallback, useEffect, useRef, useState } from "react";
import { Box, CircularProgress } from "@mui/material";
import { api } from "./api/client";
import Header from "./components/Header";
import { useTheme } from "./components/ThemeSwitcher";
import { useScrollFade } from "./hooks/useScrollFade";
import LandingPage from "./components/LandingPage";
import Footer from "./components/Footer";
import FloatingMenu from "./components/landing_components/FloatingMenu";
import IntroductionPage from "./components/IntroductionPage";
import NextGenPage from "./components/NextGenPage";
import MinistryPage from "./components/MinistryPage";
import OnlineGivingPage from "./components/OnlineGivingPage";
import JuboPage from "./components/JuboPage";
import NoticeViewPage from "./components/NoticeViewPage";
import ObituaryViewPage from "./components/ObituaryViewPage";
import BulletinViewPage from "./components/BulletinViewPage";


export default function App() {
  const [theme, setTheme] = useTheme();
  useScrollFade();
  const hasPathInitializedRef = useRef(false);
  const [currentPath, setCurrentPath] = useState(() => window.location.pathname);
  const isIntroductionPage = currentPath.startsWith("/introduction");
  const isMinistryPage = currentPath.startsWith("/ministry");
  const isOnlineGivingPage = currentPath.startsWith("/online-giving");
  const isNoticeViewPage = /^\/news\/notice\/\d+$/.test(currentPath);
  const isObituaryViewPage = /^\/news\/obituary\/\d+$/.test(currentPath);
  const isBulletinViewPage = /^\/news\/bulletin\/\d+$/.test(currentPath);
  const isJuboPage = currentPath.startsWith("/news") && !isNoticeViewPage && !isObituaryViewPage && !isBulletinViewPage;
  const isNextGenSubmenuPage = currentPath.startsWith("/nextgen");
  const [hero, setHero] = useState(null);
  const [quickLinks, setQuickLinks] = useState([]);
  const [landingTitles, setLandingTitles] = useState([]);
  const [members, setMembers] = useState([]);
  const [sections, setSections] = useState([]);
  const [visionStatements, setVisionStatements] = useState([]);
  const [sermons, setSermons] = useState([]);
  const [togetherItems, setTogetherItems] = useState([]);
  const [bulletins, setBulletins] = useState([]);
  const [latestBulletin, setLatestBulletin] = useState(null);
  const [notices, setNotices] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [ministries, setMinistries] = useState([]);
  const [obituaries, setObituaries] = useState([]);
  const [serviceTimes, setServiceTimes] = useState([]);
  const [shuttleBusSchedule, setShuttleBusSchedule] = useState([]);
  const [parkingLot, setParkingLot] = useState([]);
  const [parkingMap, setParkingMap] = useState(null);
  const [bannerImage, setBannerImage] = useState(null);
  const [pastorIntroduction, setPastorIntroduction] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  // Hashes on these paths are tab selectors, not scroll anchors
  const isTabHashPage = useCallback((path) => {
    return path.startsWith("/ministry") || path.startsWith("/nextgen") || path.startsWith("/news");
  }, []);

  const scrollToHash = useCallback(() => {
    if (isTabHashPage(window.location.pathname)) return;
    if (sessionStorage.getItem("goToContacts") === "1") {
      sessionStorage.removeItem("goToContacts");
      document.getElementById("contacts")?.scrollIntoView({ behavior: "smooth", block: "start" });
      return;
    }
    const hashTarget = window.location.hash?.replace("#", "");
    if (hashTarget) {
      // Defer to allow new page component to mount first
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          document.getElementById(hashTarget)?.scrollIntoView({ behavior: "smooth", block: "start" });
        });
      });
    }
  }, []);

  useEffect(() => {
    const syncPath = () => setCurrentPath(window.location.pathname);
    window.addEventListener("popstate", syncPath);
    window.addEventListener("locationchange", syncPath);
    window.addEventListener("hashchange", scrollToHash);

    return () => {
      window.removeEventListener("popstate", syncPath);
      window.removeEventListener("locationchange", syncPath);
      window.removeEventListener("hashchange", scrollToHash);
    };
  }, [scrollToHash]);

  useEffect(() => {
    const trackPageView = () => {
      if (typeof window.gtag === "function") {
        window.gtag("event", "page_view", {
          page_path: window.location.pathname + window.location.hash,
          page_location: window.location.href,
        });
      }
    };
    window.addEventListener("locationchange", trackPageView);
    window.addEventListener("hashchange", trackPageView);
    return () => {
      window.removeEventListener("locationchange", trackPageView);
      window.removeEventListener("hashchange", trackPageView);
    };
  }, []);

  useEffect(() => {
    let mounted = true;
    async function load() {
      setLoading(true);
      setError("");

      // ── Phase 1: landing-page essentials (blocks the loading spinner) ──────
      try {
        const [
          heroResponse,
          quickLinkResponse,
          landingTitleResponse,
          sectionsResponse,
          bannerImageResponse,
        ] = await Promise.all([
          api.getHero(),
          api.getQuickLinks(),
          api.getLandingTitles(),
          api.getSections(),
          api.getBannerImage(),
        ]);

        if (!mounted) return;
        setHero({
          backgroundImages: heroResponse?.data?.background_images ?? heroResponse?.data ?? [],
          front_images: heroResponse?.data?.front_images ?? [],
        });
        setQuickLinks(quickLinkResponse?.data ?? []);
        setLandingTitles(landingTitleResponse?.data || []);
        setSections(sectionsResponse?.data ?? []);
        setBannerImage(bannerImageResponse?.data ?? null);
        setLoading(false); // release spinner — landing page is ready
      } catch (e) {
        if (!mounted) return;
        setError(e.message || "Failed to connect backend API");
        setLoading(false);
        return;
      }

      // ── Phase 2: secondary content (silent, no spinner) ───────────────────
      try {
        const [
          sermonsResponse,
          latestBulletinResponse,
          noticesResponse,
          togetherResponse,
          departmentsResponse,
          serviceTimesResponse,
          shuttleBusScheduleResponse,
          parkingLotResponse,
          parkingMapResponse,
          memberResponse,
          visionStatementsResponse,
          ministriesResponse,
          pastorIntroductionResponse,
        ] = await Promise.all([
          api.getSermons({ page: 1, limit: 100 }),
          api.getLastBulletin(),
          api.getNotice({ page: 1, limit: 50 }),
          api.getTogether(),
          api.getDepartments(),
          api.getServiceTimes(),
          api.getShuttleBusSchedule(),
          api.getParkingLot(),
          api.getParkingMap(),
          api.getMembers(),
          api.getVisionStatements(),
          api.getMinistry(),
          api.getPastorIntroduction(),
        ]);

        if (!mounted) return;
        setSermons(sermonsResponse?.data?.data ?? sermonsResponse?.data ?? []);
        setLatestBulletin(latestBulletinResponse?.data ?? null);
        setNotices(noticesResponse?.data?.data ?? noticesResponse?.data ?? []);
        setTogetherItems(togetherResponse?.data?.data ?? togetherResponse?.data ?? []);
        setDepartments(departmentsResponse?.data?.data ?? departmentsResponse?.data ?? []);
        setServiceTimes(serviceTimesResponse?.data ?? []);
        setShuttleBusSchedule(shuttleBusScheduleResponse?.data ?? []);
        setParkingLot(parkingLotResponse?.data ?? []);
        setParkingMap(parkingMapResponse?.data ?? null);
        setMembers(memberResponse?.data?.data ?? memberResponse?.data ?? []);
        setVisionStatements(visionStatementsResponse?.data ?? []);
        setMinistries(ministriesResponse?.data?.data ?? ministriesResponse?.data ?? []);
        setPastorIntroduction(pastorIntroductionResponse?.data ?? null);
      } catch (_) {
        // Secondary content failures are non-fatal
      }
    }

    load();
    return () => {
      mounted = false;
    };
  }, []);

  useEffect(() => {
    if (loading) return;
    scrollToHash();
  }, [loading]); // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (loading) return;
    scrollToHash();
  }, [currentPath]); // eslint-disable-line react-hooks/exhaustive-deps

  // Re-scroll when pastor data arrives — prevents landing on IntroPastor when
  // scrollToHash fires before the async data has loaded and caused a layout shift.
  useEffect(() => {
    if (!loading && window.location.pathname.startsWith("/introduction") && window.location.hash) {
      scrollToHash();
    }
  }, [pastorIntroduction]); // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (loading) return;

    // Skip initial mount to avoid unnecessary animation on first render.
    if (!hasPathInitializedRef.current) {
      hasPathInitializedRef.current = true;
      return;
    }

    const hasHash = Boolean(window.location.hash);
    const isDetailPath = /^\/news\/(notice|obituary|bulletin)\/\d+$/.test(currentPath);

    // Hash-based pages and detail views already control their own scroll position.
    if (hasHash || isTabHashPage(currentPath) || isDetailPath) {
      return;
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
  }, [currentPath, loading, isTabHashPage]);

  console.log("hero", hero);
  console.log("quickLinks", quickLinks);
  console.log("landingTitles", landingTitles);
  console.log("members", members);
  console.log("sections", sections);
  console.log("visionStatements", visionStatements);
  console.log("sermons", sermons);
  console.log("togetherItems", togetherItems);
  console.log("latestBulletin", latestBulletin);
  console.log("departments", departments);
  console.log("shuttleBusSchedule", shuttleBusSchedule);
  console.log("parkingLot", parkingLot);
  console.log("parkingMap", parkingMap);
  console.log("bannerImage", bannerImage);
  console.log("ministries", ministries);

  return (
    <Box>
      <Header quickLinks={quickLinks} landingTitles={landingTitles} theme={theme} setTheme={setTheme} notice={notices} />

      {isIntroductionPage ? (
        <IntroductionPage togetherItems={togetherItems} members={members} visionStatements={visionStatements} pastorIntroduction={pastorIntroduction} />
      ) : isMinistryPage ? (
        <MinistryPage ministries={ministries} />
      ) : isOnlineGivingPage ? (
        <OnlineGivingPage />
      ) : isNoticeViewPage ? (
        <NoticeViewPage notices={notices} />
      ) : isObituaryViewPage ? (
        <ObituaryViewPage obituaries={obituaries} />
      ) : isBulletinViewPage ? (
        <BulletinViewPage />
      ) : isJuboPage ? (
        <JuboPage notices={notices} obituaries={obituaries} bulletins={bulletins} />
      ) : isNextGenSubmenuPage ? (
        <NextGenPage departments={departments.filter(d => d.department_type === 'nextgen')} />
      ) : (
        <LandingPage
          hero={hero}
          quickLinks={quickLinks}
          sermons={sermons}
          departments={departments}
          serviceTimes={serviceTimes}
          latestBulletin={latestBulletin}
          notices={notices}
          shuttleBusSchedule={shuttleBusSchedule}
          parkingLot={parkingLot}
          parkingMap={parkingMap}
          bannerImage={bannerImage}
          sections={sections}
          togetherItems={togetherItems}
        />
      )}
      <Footer landingTitles={landingTitles} />
      {isIntroductionPage || isMinistryPage || isOnlineGivingPage || isNoticeViewPage || isObituaryViewPage || isBulletinViewPage || isJuboPage || isNextGenSubmenuPage ? null : <FloatingMenu quickLinks={quickLinks} />}
    </Box>
  );
}
