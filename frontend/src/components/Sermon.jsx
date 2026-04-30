import { Card, CardContent, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";
import SectionCard from "./SectionCard";

export default function Sermon({ items = [] }) {
  const speakerList = items
    .map((item) => item.speaker)
    .filter(Boolean)
    .slice(0, 3)
    .map((speaker) => `Speaker: ${speaker}`);

  return (
    <Container id="sermon" maxWidth="lg" sx={{ py: 2 }}>
      <SectionCard
        title="Sermon"
        subtitle="Latest sermons from API"
        apiItems={speakerList}
      >
        <Typography variant="body2" color="text.secondary">
          Total loaded: {items.length}
        </Typography>
      </SectionCard>
      <Grid container spacing={2}>
        {items.slice(0, 3).map((item) => (
          <Grid key={item.id} size={{ xs: 12, md: 4 }}>
            <Card sx={{ borderRadius: 4, height: "100%" }}>
              <CardContent>
                <Stack spacing={1}>
                  <Typography variant="h6">{item.title}</Typography>
                  <Typography variant="body2" color="text.secondary">
                    {item.speaker || "-"}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    {item.youtube_url || ""}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    {item.sermon_date || ""}
                  </Typography>
                </Stack>
              </CardContent>
            </Card>
          </Grid>
        ))}
        {!items.length ? (
          <Grid size={12}>
            <Typography color="text.secondary">No sermon data.</Typography>
          </Grid>
        ) : null}
      </Grid>
    </Container>
  );
}
