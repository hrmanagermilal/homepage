import { Box, Chip, Container, Paper, Stack, Typography } from "@mui/material";

export default function Hero({ apiStatus, hero = null, heroLinks = [] }) {
  console.log("Hero data:", hero);
  console.log("Hero links:", heroLinks);
  const backgroundImages = hero?.backgroundImages ?? [];

  return (
    <Box id="hero" sx={{ pt: { xs: 3, md: 5 }, pb: 3 }}>
      <Container maxWidth="lg">
        <Paper
          elevation={0}
          sx={{
            borderRadius: 6,
            p: { xs: 3, md: 6 },
            color: "white",
            background: "linear-gradient(120deg, #0d5c63 0%, #164f7a 100%)",
            position: "relative",
            overflow: "hidden",
          }}
        >
          <Box
            sx={{
              position: "absolute",
              width: 240,
              height: 240,
              borderRadius: "50%",
              background: "rgba(255,255,255,0.08)",
              top: -80,
              right: -40,
            }}
          />
          <Stack spacing={2} className="fade-up">
            <Typography variant="h3">Welcome To Milal Church</Typography>
            <Stack direction="row" spacing={1}>
              <Chip
                label={`Backend: ${apiStatus || "Checking"}`}
                sx={{ bgcolor: "rgba(255,255,255,0.16)", color: "white" }}
              />
            </Stack>
            {hero?.subtitle && (
              <Typography variant="h6" sx={{ opacity: 0.85 }}>
                {hero.subtitle}
              </Typography>
            )}
            {hero?.description && (
              <Typography variant="body1" sx={{ opacity: 0.75, maxWidth: 560 }}>
                {hero.description}
              </Typography>
            )}
            {backgroundImages.length > 0 && (
              <Stack spacing={0.5}>
                {backgroundImages.map((img, idx) => (
                  <Typography key={img.id ?? idx} variant="body2" sx={{ opacity: 0.75 }}>
                    {img.image_url}
                  </Typography>
                ))}
              </Stack>
            )}
            <Typography key={hero?.frontImage?.id ?? "frontImage"} variant="body2" sx={{ opacity: 0.75 }}>
                {hero?.frontImage?.image_url}
            </Typography>

            {heroLinks?.length > 0 && (
              <Stack spacing={0.5}>
                {heroLinks.map((link, idx) => (
                  <Typography key={link.id ?? idx} variant="body2" sx={{ opacity: 0.75 }}>
                    {link.title} / {link.link_url}
                  </Typography>
                ))}
              </Stack>
            )}
          </Stack>
        </Paper>
      </Container>
    </Box>
  );
}
