import { AppBar, Box, Button, Container, Stack, Toolbar, Typography } from "@mui/material";

const menuItems = [
  { label: "Home", target: "hero" },
  { label: "Sermon", target: "sermon" },
  { label: "Service", target: "service-time" },
  { label: "Jubo", target: "jubo" },
  { label: "Notice", target: "announcement" },
  { label: "Contact", target: "contacts" },
];

export default function Header({ menuLinks = [], landingTitles = [] }) {
  const moveTo = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth", block: "start" });
  };

  const landingPrimary = landingTitles[0]?.title;
  const dynamicQuickLinks = menuLinks.slice(0, 2).map((item, index) => ({
    label: item.title || `Link ${index + 1}`,
    target: "hero",
  }));
  const navItems = [...menuItems, ...dynamicQuickLinks];

  return (
    <AppBar position="sticky" elevation={0} sx={{ bgcolor: "rgba(5, 26, 32, 0.85)", backdropFilter: "blur(8px)" }}>
      <Container maxWidth="lg">
        <Toolbar disableGutters sx={{ minHeight: 72 }}>
          <Typography variant="h6" sx={{ fontWeight: 800, mr: 3 }}>
            {landingPrimary || "MILAL CHURCH"}
          </Typography>
          <Box sx={{ flexGrow: 1 }} />
          <Stack direction="row" spacing={0.5} sx={{ display: { xs: "none", md: "flex" } }}>
            {navItems.map((item, index) => (
              <Button
                key={`${item.target}-${item.label}-${index}`}
                onClick={() => moveTo(item.target)}
                sx={{ color: "white", borderRadius: 99, px: 1.7 }}
              >
                {item.label}
              </Button>
            ))}
          </Stack>
        </Toolbar>
      </Container>
    </AppBar>
  );
}
