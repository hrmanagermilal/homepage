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
import NewsPage from "./components/NewsPage";
import NoticeViewPage from "./components/NoticeViewPage";
import ObituaryViewPage from "./components/ObituaryViewPage";


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
  const isNewsPage = currentPath.startsWith("/news") && !isNoticeViewPage && !isObituaryViewPage;
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
  const [announcements, setAnnouncements] = useState([]);
  const [news, setNews] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [serviceTimes, setServiceTimes] = useState([]);
  const [shuttleBusSchedule, setShuttleBusSchedule] = useState([]);
  const [parkingLot, setParkingLot] = useState([]);
  const [parkingMap, setParkingMap] = useState(null);
  const [bannerImage, setBannerImage] = useState(null);
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
    let mounted = true;
    async function load() {
      setLoading(true);
      setError("");
      try {
        const [
          heroResponse,
          quickLinkResponse,
          landingTitleResponse,
          memberResponse,
          sectionsResponse,
          visionStatementsResponse,
          sermonsResponse,
          togetherResponse,
          bulletinsResponse,
          announcementsResponse,
          departmentsResponse,
          serviceTimesResponse,
          newsResponse,
          shuttleBusScheduleResponse,
          parkingLotResponse,
          parkingMapResponse,
          bannerImageResponse,
        ] = await Promise.all([
          api.getHeroBackgroundImages(),
          api.getQuickLinks(),
          api.getLandingTitles(),
          api.getMembers(),
          api.getSections(),
          api.getVisionStatements(),
          api.getSermons({ page: 1, limit: 100 }),
          api.getTogether(),
          api.getBulletins({ page: 1, limit: 6 }),
          api.getAnnouncements({ page: 1, limit: 5 }),
          api.getDepartments(),
          api.getServiceTimes(),
          api.getNews({ page: 1, limit: 5 }),
          api.getShuttleBusSchedule(),
          api.getParkingLot(),
          api.getParkingMap(),
          api.getBannerImage(),
        ]);

        if (!mounted) return;
        setHero({ backgroundImages: heroResponse?.data ?? [] });
        setQuickLinks(quickLinkResponse?.data ?? []);
        setLandingTitles(landingTitleResponse?.data || []);
        setMembers(memberResponse?.data?.data ?? memberResponse?.data ?? []);
        setSections(sectionsResponse?.data ?? []);
        setVisionStatements(visionStatementsResponse?.data ?? []);
        setSermons(sermonsResponse?.data?.data ?? sermonsResponse?.data ?? []);
        setTogetherItems(togetherResponse?.data?.data ?? togetherResponse?.data ?? []);
        setBulletins(bulletinsResponse?.data?.data ?? bulletinsResponse?.data ?? []);
        setAnnouncements(announcementsResponse?.data?.data ?? announcementsResponse?.data ?? []);
        setNews(newsResponse?.data?.data ?? newsResponse?.data ?? []);
        setDepartments(departmentsResponse?.data?.data ?? departmentsResponse?.data ?? []);
        setServiceTimes(serviceTimesResponse?.data ?? []);
        setShuttleBusSchedule(shuttleBusScheduleResponse?.data ?? []);
        setParkingLot(parkingLotResponse?.data ?? []);
        setParkingMap(parkingMapResponse?.data ?? null);
        setBannerImage(bannerImageResponse?.data ?? null);
      } catch (e) {
        if (!mounted) return;
        setError(e.message || "Failed to connect backend API");
      } finally {
        if (mounted) setLoading(false);
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

  useEffect(() => {
    if (loading) return;

    // Skip initial mount to avoid unnecessary animation on first render.
    if (!hasPathInitializedRef.current) {
      hasPathInitializedRef.current = true;
      return;
    }

    const hasHash = Boolean(window.location.hash);
    const isDetailPath = /^\/news\/(notice|obituary)\/\d+$/.test(currentPath);

    // Hash-based pages and detail views already control their own scroll position.
    if (hasHash || isTabHashPage(currentPath) || isDetailPath) {
      return;
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
  }, [currentPath, loading, isTabHashPage]);

  console.log("quickLinks", quickLinks);
  console.log("landingTitles", landingTitles);
  console.log("members", members);
  console.log("sections", sections);
  console.log("visionStatements", visionStatements);
  console.log("sermons", sermons);
  console.log("togetherItems", togetherItems);
  console.log("bulletins", bulletins);
  console.log("announcements", announcements);
  console.log("departments", departments);
  console.log("serviceTimes", serviceTimes);
  console.log("news", news);
  console.log("shuttleBusSchedule", shuttleBusSchedule);
  console.log("parkingLot", parkingLot);
  console.log("parkingMap", parkingMap);

  return (
    <Box>
      <Header quickLinks={quickLinks} landingTitles={landingTitles} theme={theme} setTheme={setTheme} />

      {isIntroductionPage ? (
        <IntroductionPage togetherItems={togetherItems} members={members} visionStatements={visionStatements} />
      ) : isMinistryPage ? (
        <MinistryPage />
      ) : isOnlineGivingPage ? (
        <OnlineGivingPage />
      ) : isNoticeViewPage ? (
        <NoticeViewPage />
      ) : isObituaryViewPage ? (
        <ObituaryViewPage />
      ) : isNewsPage ? (
        <NewsPage />
      ) : isNextGenSubmenuPage ? (
        <NextGenPage />
      ) : (
        <LandingPage
          hero={hero}
          quickLinks={quickLinks}
          sermons={sermons}
          departments={departments}
          serviceTimes={serviceTimes}
          bulletins={bulletins}
          announcements={announcements}
          news={news}
          shuttleBusSchedule={shuttleBusSchedule}
          parkingLot={parkingLot}
          parkingMap={parkingMap}
          bannerImage={bannerImage}
          sections={sections}
          togetherItems={togetherItems}
        />
      )}
      <Footer landingTitles={landingTitles} />
      {isIntroductionPage || isMinistryPage || isOnlineGivingPage || isNoticeViewPage || isObituaryViewPage || isNewsPage || isNextGenSubmenuPage ? null : <FloatingMenu quickLinks={quickLinks} />}
    </Box>
  );
}
