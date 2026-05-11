import { useState } from "react";
import MenuIcon from "@mui/icons-material/Menu";
import VolumeUpIcon from "@mui/icons-material/VolumeUp";
import BookmarkIcon from "@mui/icons-material/Bookmark";
import { AppBar, Box, Button, Container, IconButton, Menu, MenuItem, Stack, Toolbar, Typography } from "@mui/material";

const NAV_ITEMS = [
  { label: "Introduction", path: "/introduction" },
  { label: "다음세대", hasSubmenu: true },
  { label: "사역", path: "/ministry" },
  { label: "소식", hasNewsSubmenu: true },
  { label: "온라인 헌금", path: "/online-giving" },
];

const NEXTGEN_SUBMENUS = [
  { label: "청년부", path: "/nextgen/young-adults" },
  { label: "KM 청소년부", path: "/nextgen/km-youth" },
  { label: "EM 청소년부", path: "/nextgen/em-youth" },
  { label: "아동부", path: "/nextgen/children" },
  { label: "유치부", path: "/nextgen/kindergarten" },
  { label: "유아부", path: "/nextgen/preschool" },
  { label: "영아부", path: "/nextgen/infants" },
];

const NEWS_SUBMENUS = [
  { label: "공지", path: "/news/notice" },
  { label: "부고", path: "/news/obituary" },
];

export default function Header({ quickLinks = [], landingTitles = [] }) {
  const currentPath = window.location.pathname;
  const isIntroductionPage = currentPath.startsWith("/introduction");
  const [nextgenAnchorEl, setNextgenAnchorEl] = useState(null);
  const [newsAnchorEl, setNewsAnchorEl] = useState(null);
  const isNextgenMenuOpen = Boolean(nextgenAnchorEl);
  const isNewsMenuOpen = Boolean(newsAnchorEl);

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

  const openNextgenMenu = (event) => {
    setNextgenAnchorEl(event.currentTarget);
  };

  const closeNextgenMenu = () => {
    setNextgenAnchorEl(null);
  };

  const openNewsMenu = (event) => {
    setNewsAnchorEl(event.currentTarget);
  };

  const closeNewsMenu = () => {
    setNewsAnchorEl(null);
  };

  const navigateToSubmenu = (path) => {
    closeNextgenMenu();
    closeNewsMenu();
    window.location.href = path;
  };

  const navigateToPath = (path) => {
    if (currentPath === path) {
      window.scrollTo({ top: 0, behavior: "smooth" });
      return;
    }
    window.location.href = path;
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
                onClick={(event) => {
                  if (item.hasSubmenu) {
                    openNextgenMenu(event);
                    return;
                  }
                  if (item.hasNewsSubmenu) {
                    openNewsMenu(event);
                    return;
                  }
                  if (item.path) {
                    navigateToPath(item.path);
                    return;
                  }
                  moveTo(item.target);
                }}
                sx={{ color: "white", borderRadius: 0, px: 2, fontSize: "0.9rem", fontWeight: 500, "&:hover": { bgcolor: "rgba(255,255,255,0.08)" } }}
              >
                {item.label}
              </Button>
            ))}
          </Stack>

          <Menu
            anchorEl={nextgenAnchorEl}
            open={isNextgenMenuOpen}
            onClose={closeNextgenMenu}
            anchorOrigin={{ vertical: "bottom", horizontal: "left" }}
            transformOrigin={{ vertical: "top", horizontal: "left" }}
          >
            {NEXTGEN_SUBMENUS.map((submenu) => (
              <MenuItem key={submenu.path} onClick={() => navigateToSubmenu(submenu.path)}>
                {submenu.label}
              </MenuItem>
            ))}
          </Menu>

          <Menu
            anchorEl={newsAnchorEl}
            open={isNewsMenuOpen}
            onClose={closeNewsMenu}
            anchorOrigin={{ vertical: "bottom", horizontal: "left" }}
            transformOrigin={{ vertical: "top", horizontal: "left" }}
          >
            {NEWS_SUBMENUS.map((submenu) => (
              <MenuItem key={submenu.path} onClick={() => navigateToSubmenu(submenu.path)}>
                {submenu.label}
              </MenuItem>
            ))}
          </Menu>

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
