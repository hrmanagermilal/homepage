import KeyboardArrowUpRoundedIcon from "@mui/icons-material/KeyboardArrowUpRounded";
import PhoneRoundedIcon from "@mui/icons-material/PhoneRounded";
import { Box, Fab, Link, Paper, Stack, Typography } from "@mui/material";

export default function FloatingMenu({ quickLinks = [] }) {
  const actionLink = quickLinks[0]?.link_url;

  return (
    <Box sx={{ position: "fixed", right: 20, bottom: 20, zIndex: 1200 }}>
      {quickLinks.length ? (
        <Paper sx={{ mb: 1, p: 1.2, borderRadius: 3, maxWidth: 220 }}>
          <Stack spacing={0.5}>
            <Typography variant="caption" color="text.secondary">
              Quick Links
            </Typography>
            {quickLinks.slice(0, 3).map((link) => (
              <Link key={link.id} href={link.link_url || "#"} underline="hover" variant="caption">
                {link.title || "Link"}
              </Link>
            ))}
          </Stack>
        </Paper>
      ) : null}
      <Stack spacing={1.2}>
        <Fab
          size="medium"
          color="primary"
          onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
        >
          <KeyboardArrowUpRoundedIcon />
        </Fab>
        <Fab
          size="medium"
          color="secondary"
          onClick={() => {
            if (actionLink) {
              window.open(actionLink, "_blank", "noopener,noreferrer");
              return;
            }
            document.getElementById("contacts")?.scrollIntoView({ behavior: "smooth" });
          }}
        >
          <PhoneRoundedIcon />
        </Fab>
      </Stack>
    </Box>
  );
}
