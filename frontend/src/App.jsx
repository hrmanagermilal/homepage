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

export default function App() {
  const [health, setHealth] = useState(null);
  const [hero, setHero] = useState(null);
  const [heroLinks, setHeroLinks] = useState([]);
  const [landingTitles, setLandingTitles] = useState([]);
  const [members, setMembers] = useState([]);
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
        const [h, heroResponse, heroLinkResponse, landingTitleResponse, memberResponse, s, t, b, a, d] = await Promise.all([
          api.getHealth(),
          api.getHero(),
          api.getHeroLinks(),
          api.getLandingTitles(),
          api.getMembers(),
          api.getSermons({ page: 1, limit: 5 }),
          api.getTogether(),
          api.getBulletins({ page: 1, limit: 6 }),
          api.getAnnouncements({ page: 1, limit: 5 }),
          api.getDepartments(),
        ]);

        if (!mounted) return;
        setHealth(h?.message || "Online");
        setHero(heroResponse?.data ?? null);
        setHeroLinks(heroLinkResponse?.data ?? []);
        setLandingTitles(landingTitleResponse?.data || []);
        setMembers(memberResponse?.data?.data ?? memberResponse?.data ?? []);
        setSermons(s?.data?.data ?? s?.data ?? []);
        setTogetherItems(t?.data?.data ?? t?.data ?? []);
        setBulletins(b?.data?.data ?? b?.data ?? []);
        setAnnouncements(a?.data?.data ?? a?.data ?? []);
        setDepartments(d?.data?.data ?? d?.data ?? []);
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

  return (
    <Box>
      <Header menuLinks={heroLinks} landingTitles={landingTitles} />
      <Hero apiStatus={health} hero={hero} heroLinks={heroLinks}/>

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

      <Sermon items={sermons} />
      <ServiceTime departments={departments} />
      <Jubo items={bulletins} />
      <Announcement items={announcements} />
      <Contacts members={members} />
      <FooterTop items={togetherItems} />
      <Footer landingTitles={landingTitles} heroLinks={heroLinks} />
      <FloatingMenu quickLinks={heroLinks} />
    </Box>
  );
}
