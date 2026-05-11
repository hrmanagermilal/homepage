import { useEffect, useState } from "react";
import { Alert, Box, CircularProgress, Stack } from "@mui/material";
import { api } from "./api/client";
import Header from "./components/Header";
import Hero from "./components/Hero";
import Sermon from "./components/Sermon";
import ServiceTime from "./components/ServiceTime";
import Jubo from "./components/Jubo";
import Announcement from "./components/Announcement";
import Contacts from "./components/Contacts";
import FooterTop from "./components/FooterTop";
import Footer from "./components/Footer";
import FloatingMenu from "./components/FloatingMenu";
import IntroductionPage from "./components/IntroductionPage";
import NextGenPage from "./components/NextGenPage";
import MinistryPage from "./components/MinistryPage";
import OnlineGivingPage from "./components/OnlineGivingPage";
import NoticePage from "./components/NoticePage";
import ObituaryPage from "./components/ObituaryPage";

const NEXTGEN_PAGE_TITLES = {
  "/nextgen/young-adults": "청년부",
  "/nextgen/km-youth": "KM 청소년부",
  "/nextgen/em-youth": "EM 청소년부",
  "/nextgen/children": "아동부",
  "/nextgen/kindergarten": "유치부",
  "/nextgen/preschool": "유아부",
  "/nextgen/infants": "영아부",
};

export default function App() {
  const currentPath = window.location.pathname;
  const isIntroductionPage = window.location.pathname.startsWith("/introduction");
  const isMinistryPage = currentPath.startsWith("/ministry");
  const isOnlineGivingPage = currentPath.startsWith("/online-giving");
  const isNoticePage = currentPath.startsWith("/news/notice");
  const isObituaryPage = currentPath.startsWith("/news/obituary");
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
      {isIntroductionPage || isMinistryPage || isOnlineGivingPage || isNoticePage || isObituaryPage || isNextGenSubmenuPage ? null : <Hero apiStatus={health} hero={hero} heroLinks={heroLinks} />}

      {loading ? (
        <Stack alignItems="center" py={8}>
          <CircularProgress />
        </Stack>
      ) : null}

      {error ? (
        <Box sx={{ px: { xs: 2, md: 6 }, mb: 2 }}>
          <Alert severity="error">
            {error}. Check VITE_API_BASE_URL and backend server status.
          </Alert>
        </Box>
      ) : null}

      {isIntroductionPage ? (
        <IntroductionPage togetherItems={togetherItems} members={members} visionStatements={visionStatements} />
      ) : isMinistryPage ? (
        <MinistryPage />
      ) : isOnlineGivingPage ? (
        <OnlineGivingPage />
      ) : isNoticePage ? (
        <NoticePage />
      ) : isObituaryPage ? (
        <ObituaryPage />
      ) : isNextGenSubmenuPage ? (
        <NextGenPage title={nextGenPageTitle} />
      ) : (
        <>
          <Sermon items={sermons} section={sections.find((s) => s.title === "최신 설교")} />
          <ServiceTime departments={departments} section={sections.find((s) => s.title === "예배 시간")} />
          <Jubo items={bulletins} section={sections.find((s) => s.title === "주보")} />
          <Announcement items={announcements} section={sections.find((s) => s.title === "공지사항")} />

          <FooterTop items={togetherItems} section={sections.find((s) => s.title === "함께하는 교회")} />
        </>
      )}
      <Footer landingTitles={landingTitles} heroLinks={heroLinks} />
      {isIntroductionPage || isMinistryPage || isOnlineGivingPage || isNoticePage || isObituaryPage || isNextGenSubmenuPage ? null : <FloatingMenu quickLinks={quickLinks} />}
    </Box>
  );
}
