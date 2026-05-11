import MenuIcon from "@mui/icons-material/Menu";
import VolumeUpIcon from "@mui/icons-material/VolumeUp";
import BookmarkIcon from "@mui/icons-material/Bookmark";
import { AppBar, Box, Button, Container, IconButton, Stack, Toolbar, Typography } from "@mui/material";

const NAV_ITEMS = [
  { label: "Introduction", path: "/introduction" },
  { label: "다음세대", target: "sermon" },
  { label: "사역", target: "service-time" },
  { label: "소식", target: "announcement" },
  { label: "온라인 헌금", target: "contacts" },
];

export default function Header({ quickLinks = [], landingTitles = [] }) {
  const isIntroductionPage = window.location.pathname.startsWith("/introduction");

  const moveHome = (hash = "") => {
    window.location.href = hash ? `/#${hash}` : "/";
  };

  const moveTo = (id) => {
    const target = document.getElementById(id);
    if (target) {
      target.scrollIntoView({ behavior: "smooth", block: "start" });
      return;
    }
    moveHome(id);
  };

  const navigateIntroduction = () => {
    if (isIntroductionPage) {
      window.scrollTo({ top: 0, behavior: "smooth" });
      return;
    }
    window.location.href = "/introduction";
  };

  const handleLogoClick = () => {
    if (isIntroductionPage) {
      moveHome();
      return;
    }
    moveTo("hero");
  };

  const quickLinkUrl = quickLinks[0]?.link || "#";
  const quickLinkLabel = quickLinks[0]?.title || "밀알 소식 바로가기";

  return (
    <AppBar position="sticky" elevation={0} sx={{ bgcolor: "rgba(5, 26, 32, 0.85)", backdropFilter: "blur(8px)" }}>
      <Container maxWidth="xl">
        <Toolbar disableGutters sx={{ minHeight: 72, gap: 2 }}>
          {/* Logo */}
          <Stack direction="row" alignItems="center" spacing={1} sx={{ mr: 2, cursor: "pointer" }} onClick={handleLogoClick}>
            <Box
              component="img"
              src="/logo.png"
              alt="밀알교회"
              sx={{ height: 40, objectFit: "contain" }}
              onError={(e) => { e.currentTarget.style.display = "none"; }}
            />
            <Stack spacing={0}>
              <Typography variant="subtitle2" sx={{ fontWeight: 800, lineHeight: 1.1, color: "white" }}>
                밀알교회
              </Typography>
              <Typography variant="caption" sx={{ color: "rgba(255,255,255,0.6)", lineHeight: 1, letterSpacing: 1 }}>
                MILAL CHURCH
              </Typography>
            </Stack>
          </Stack>

          {/* Volume icon */}
          <IconButton sx={{ color: "rgba(255,255,255,0.7)" }} size="small">
            <VolumeUpIcon fontSize="small" />
          </IconButton>

          {/* Nav */}
          <Stack direction="row" spacing={0} sx={{ display: { xs: "none", md: "flex" }, flexGrow: 1 }}>
            {NAV_ITEMS.map((item) => (
              <Button
                key={item.label}
                onClick={() => (item.path ? navigateIntroduction() : moveTo(item.target))}
                sx={{ color: "white", borderRadius: 0, px: 2, fontSize: "0.9rem", fontWeight: 500, "&:hover": { bgcolor: "rgba(255,255,255,0.08)" } }}
              >
                {item.label}
              </Button>
            ))}
          </Stack>

          <Box sx={{ flexGrow: 1, display: { md: "none" } }} />

          {/* Quick link button */}
          <Button
            href={quickLinkUrl}
            target="_blank"
            rel="noopener noreferrer"
            startIcon={<BookmarkIcon />}
            variant="contained"
            sx={{
              display: { xs: "none", md: "flex" },
              bgcolor: "#2e7d6b",
              color: "white",
              borderRadius: 1,
              px: 2,
              fontWeight: 600,
              whiteSpace: "nowrap",
              "&:hover": { bgcolor: "#236055" },
            }}
          >
            {quickLinkLabel}
          </Button>

          {/* Hamburger */}
          <IconButton sx={{ color: "white", ml: 0.5 }}>
            <MenuIcon />
          </IconButton>
        </Toolbar>
      </Container>
    </AppBar>
  );
}
