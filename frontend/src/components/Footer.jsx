import { Box, Container, Stack, Typography } from "@mui/material";

export default function Footer({ landingTitles = [], heroLinks = [] }) {
  const footerTitle = landingTitles[0]?.title || "Milal Church";
  const footerDescription = landingTitles[0]?.descriptions || "";
  const footerLinks = heroLinks.slice(0, 3).map((item) => item.title).filter(Boolean).join(" | ");

  return (
    <Box sx={{ py: 3, bgcolor: "#071318", color: "#b8d2d6" }}>
      <Container maxWidth="lg">
        <Stack direction={{ xs: "column", md: "row" }} justifyContent="space-between" spacing={1}>
          <Stack spacing={0.3}>
            <Typography variant="body2">{footerTitle}</Typography>
            {footerDescription ? <Typography variant="caption">{footerDescription}</Typography> : null}
            {footerLinks ? <Typography variant="caption">{footerLinks}</Typography> : null}
          </Stack>
          <Typography variant="body2">Copyright {new Date().getFullYear()}. All rights reserved.</Typography>
        </Stack>
      </Container>
    </Box>
  );
}
