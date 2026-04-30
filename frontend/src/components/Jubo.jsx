import { Card, CardContent, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";

export default function Jubo({ items = [] }) {
  return (
    <Container id="jubo" maxWidth="lg" sx={{ py: 2 }}>
      <Typography variant="h4" sx={{ mb: 2, fontWeight: 800 }}>
        Jubo
      </Typography>
      <Grid container spacing={2}>
        {items.slice(0, 4).map((item) => (
          <Grid key={item.id} size={{ xs: 12, md: 3 }}>
            <Card sx={{ borderRadius: 4 }}>
              <CardContent>
                <Stack spacing={0.6}>
                  <Typography variant="h6">{item.title}</Typography>
                  <Typography variant="body2" color="text.secondary">
                    Week {item.week_number || "-"} / {item.year || "-"}
                  </Typography>
                  <Typography variant="caption" color="text.secondary">
                    Updated: {item.updated_at ? new Date(item.updated_at).toLocaleDateString() : "-"}
                  </Typography>
                </Stack>
              </CardContent>
            </Card>
          </Grid>
        ))}
        {!items.length ? (
          <Grid size={12}>
            <Typography color="text.secondary">No bulletin data.</Typography>
          </Grid>
        ) : null}
      </Grid>
    </Container>
  );
}
