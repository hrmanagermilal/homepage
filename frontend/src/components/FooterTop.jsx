import { Box, Button, Container, Stack, Typography } from "@mui/material";

export default function FooterTop({ items = [] }) {
  const primary = items[0];
  const title = primary?.title || "Join Sunday Worship With Us";
  const link = primary?.link || "#";

  return (
    <Box sx={{ mt: 4, py: 4, background: "linear-gradient(120deg, #1a4f63, #0d5c63)", color: "white" }}>
      <Container maxWidth="lg">
        <Stack direction={{ xs: "column", md: "row" }} spacing={2} justifyContent="space-between" alignItems="center">
          <Typography variant="h5" sx={{ fontWeight: 800 }}>
            {title}
          </Typography>
          <Button variant="contained" color="secondary" sx={{ px: 3 }} href={link}>
            {primary?.title ? "Learn More" : "Visit Milal Church"}
          </Button>
        </Stack>
        {items.length > 1 ? (
          <Stack direction="row" spacing={1.5} sx={{ mt: 2, flexWrap: "wrap" }}>
            {items.slice(1, 5).map((item) => (
              <Typography key={item.id} variant="caption" sx={{ opacity: 0.9 }}>
                {item.title}
              </Typography>
            ))}
          </Stack>
        ) : null}
      </Container>
    </Box>
  );
}
