import { Card, CardContent, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";

export default function ServiceTime({ departments = [] }) {
  const times = departments
    .filter((d) => d.worship_day || d.worship_time)
    .slice(0, 6);

  return (
    <Container id="service-time" maxWidth="lg" sx={{ py: 2 }}>
      <Typography variant="h4" sx={{ mb: 2, fontWeight: 800 }}>
        Service Time
      </Typography>
      <Grid container spacing={2}>
        {times.map((dep) => (
          <Grid key={dep.id} size={{ xs: 12, md: 4 }}>
            <Card sx={{ borderRadius: 4 }}>
              <CardContent>
                <Stack spacing={0.7}>
                  <Typography variant="h6">{dep.name}</Typography>
                  <Typography variant="body2" color="text.secondary">
                    {dep.worship_day || "TBA"} {dep.worship_time || ""}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    {dep.worship_location || ""}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    {dep.clergy_name ? `${dep.clergy_name} ${dep.clergy_position || ""}` : ""}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    {dep.clergy_phone || ""}
                  </Typography>
                </Stack>
              </CardContent>
            </Card>
          </Grid>
        ))}
        {!times.length ? (
          <Grid size={12}>
            <Typography color="text.secondary">No service time data.</Typography>
          </Grid>
        ) : null}
      </Grid>
    </Container>
  );
}
