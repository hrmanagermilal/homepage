import { Card, CardContent, Chip, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";

export default function Announcement({ items = [] }) {
  return (
    <Container id="announcement" maxWidth="lg" sx={{ py: 2 }}>
      <Typography variant="h4" sx={{ mb: 2, fontWeight: 800 }}>
        Announcement
      </Typography>
      <Grid container spacing={2}>
        {items.slice(0, 4).map((item) => (
          <Grid key={item.id} size={{ xs: 12, md: 6 }}>
            <Card sx={{ borderRadius: 4 }}>
              <CardContent>
                <Stack spacing={1}>
                  <Typography variant="h6">{item.title}</Typography>
                  <Typography variant="body2" color="text.secondary">
                    {item.content}
                  </Typography>
                  <Stack direction="row" spacing={1}>
                    <Chip size="small" color="secondary" label={item.category || "general"} />
                  </Stack>
                </Stack>
              </CardContent>
            </Card>
          </Grid>
        ))}
        {!items.length ? (
          <Grid size={12}>
            <Typography color="text.secondary">No announcement data.</Typography>
          </Grid>
        ) : null}
      </Grid>
    </Container>
  );
}
