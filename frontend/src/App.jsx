import { useEffect, useState } from "react";
import { Box, CircularProgress } from "@mui/material";
import { api } from "./api/client";
import Header from "./components/Header";
import LandingPage from "./components/LandingPage";
import Footer from "./components/Footer";
import FloatingMenu from "./components/landing_components/FloatingMenu";
import IntroductionPage from "./components/IntroductionPage";
import NextGenPage from "./components/NextGenPage";
import MinistryPage from "./components/MinistryPage";
import OnlineGivingPage from "./components/OnlineGivingPage";
import NoticePage from "./components/NoticePage";
import NoticeViewPage from "./components/NoticeViewPage";
import ObituaryPage from "./components/ObituaryPage";
import ObituaryViewPage from "./components/ObituaryViewPage";

const NEXTGEN_PAGE_TITLES = {
  "/nextgen/young-adults": "청년부",
  "/nextgen/km-youth": "KM 청소년부",
  "/nextgen/em-youth": "EM 청소년부",
  "/nextgen/children": "아동부",
  "/nextgen/kindergarten": "유치부",
  "/nextgen/infants": "영유아부",
};

export default function App() {
  const [currentPath, setCurrentPath] = useState(() => window.location.pathname);
  const isIntroductionPage = currentPath.startsWith("/introduction");
  const isMinistryPage = currentPath.startsWith("/ministry");
  const isOnlineGivingPage = currentPath.startsWith("/online-giving");
  const isNoticeViewPage = /^\/news\/notice\/\d+$/.test(currentPath);
  const isNoticePage = currentPath.startsWith("/news/notice") && !isNoticeViewPage;
  const isObituaryViewPage = /^\/news\/obituary\/\d+$/.test(currentPath);
  const isObituaryPage = currentPath.startsWith("/news/obituary") && !isObituaryViewPage;
  const nextGenPageTitle = NEXTGEN_PAGE_TITLES[currentPath] || null;
  const isNextGenSubmenuPage = Boolean(nextGenPageTitle);
  const [health, setHealth] = useState(null);
  const [hero, setHero] = useState(null);
  const [heroLinks, setHeroLinks] = useState([]);
  const [quickLinks, setQuickLinks] = useState([]);
  const [landingTitles, setLandingTitles] = useState([]);
  const [members, setMembers] = useState([]);
  const [sections, setSections] = useState([]);
  const [visionStatements, setVisionStatements] = useState([]);
  const [sermons, setSermons] = useState([]);
  const [togetherItems, setTogetherItems] = useState([]);
  const [bulletins, setBulletins] = useState([]);
  const [announcements, setAnnouncements] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const syncPath = () => setCurrentPath(window.location.pathname);
    window.addEventListener("popstate", syncPath);
    window.addEventListener("locationchange", syncPath);

    return () => {
      window.removeEventListener("popstate", syncPath);
      window.removeEventListener("locationchange", syncPath);
    };
  }, []);

  useEffect(() => {
    let mounted = true;
    async function load() {
      setLoading(true);
      setError("");
      try {
        const [
          healthResponse,
          heroResponse,
          heroLinkResponse,
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
        ] = await Promise.all([
          api.getHealth(),
          api.getHero(),
          api.getHeroLinks(),
          api.getQuickLinks(),
          api.getLandingTitles(),
          api.getMembers(),
          api.getSections(),
          api.getVisionStatements(),
          api.getSermons({ page: 1, limit: 5 }),
          api.getTogether(),
          api.getBulletins({ page: 1, limit: 6 }),
          api.getAnnouncements({ page: 1, limit: 5 }),
          api.getDepartments(),
        ]);

        if (!mounted) return;
        setHealth(healthResponse?.message || "Online");
        setHero(heroResponse?.data ?? null);
        setHeroLinks(heroLinkResponse?.data ?? []);
        setQuickLinks(quickLinkResponse?.data ?? []);
        setLandingTitles(landingTitleResponse?.data || []);
        setMembers(memberResponse?.data?.data ?? memberResponse?.data ?? []);
        setSections(sectionsResponse?.data ?? []);
        setVisionStatements(visionStatementsResponse?.data ?? []);
        setSermons(sermonsResponse?.data?.data ?? sermonsResponse?.data ?? []);
        setTogetherItems(togetherResponse?.data?.data ?? togetherResponse?.data ?? []);
        setBulletins(bulletinsResponse?.data?.data ?? bulletinsResponse?.data ?? []);
        setAnnouncements(announcementsResponse?.data?.data ?? announcementsResponse?.data ?? []);
        setDepartments(departmentsResponse?.data?.data ?? departmentsResponse?.data ?? []);
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
    if (loading) {
      return;
    }

    if (sessionStorage.getItem("goToContacts") === "1") {
      sessionStorage.removeItem("goToContacts");
      document.getElementById("contacts")?.scrollIntoView({ behavior: "smooth", block: "start" });
      return;
    }

    const hashTarget = window.location.hash?.replace("#", "");
    if (hashTarget) {
      document.getElementById(hashTarget)?.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }, [loading]);

  return (
    <Box>
      <Header quickLinks={quickLinks} landingTitles={landingTitles} />

      {loading ? (
        <div className="app-loading-overlay" aria-live="polite" aria-busy="true">
          <CircularProgress />
        </div>
      ) : null}

      {isIntroductionPage ? (
        <IntroductionPage togetherItems={togetherItems} members={members} visionStatements={visionStatements} />
      ) : isMinistryPage ? (
        <MinistryPage />
      ) : isOnlineGivingPage ? (
        <OnlineGivingPage />
      ) : isNoticeViewPage ? (
        <NoticeViewPage />
      ) : isNoticePage ? (
        <NoticePage />
      ) : isObituaryViewPage ? (
        <ObituaryViewPage />
      ) : isObituaryPage ? (
        <ObituaryPage />
      ) : isNextGenSubmenuPage ? (
        <NextGenPage title={nextGenPageTitle} />
      ) : (
        <LandingPage
          hero={hero}
          quickLinks={quickLinks}
          sermons={sermons}
          departments={departments}
          bulletins={bulletins}
          announcements={announcements}
          sections={sections}
          togetherItems={togetherItems}
        />
      )}
      <Footer landingTitles={landingTitles} heroLinks={heroLinks} />
      {isIntroductionPage || isMinistryPage || isOnlineGivingPage || isNoticeViewPage || isNoticePage || isObituaryViewPage || isObituaryPage || isNextGenSubmenuPage ? null : <FloatingMenu quickLinks={quickLinks} />}
    </Box>
  );
}
