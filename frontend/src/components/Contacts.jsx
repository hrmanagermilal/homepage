import { Card, CardContent, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";

export default function Contacts({ members = [], section = null }) {
  console.log("Contacts members:", members);
  const sectionTitle = section?.title ?? "Contacts";
  const sectionSubtitle = section?.subtitle ?? null;
  const contacts = members.map((member) => ({
    id: member.id,
    title: member.name || member.title || "Member",
    subtitle: member.role || member.title || "",
    value: member.email || "No email",
  }));

  return (
    <Container id="contacts" maxWidth="lg" sx={{ py: 2 }}>
      <Typography variant="h4" sx={{ mb: sectionSubtitle ? 0.5 : 2, fontWeight: 800 }}>
        {sectionTitle}
      </Typography>
      {sectionSubtitle ? (
        <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
          {sectionSubtitle}
        </Typography>
      ) : null}
      <Grid container spacing={2}>
        {contacts.map((c) => (
          <Grid key={c.id} item xs={12} md={4}>
            <Card sx={{ borderRadius: 4 }}>
              <CardContent>
                <Stack spacing={0.5}>
                  <Typography variant="overline" color="text.secondary">
                    {c.title}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    {c.subtitle}
                  </Typography>
                  <Typography variant="h6">{c.value}</Typography>
                </Stack>
              </CardContent>
            </Card>
          </Grid>
        ))}
        {!contacts.length ? (
          <Grid item xs={12}>
            <Typography color="text.secondary">No contact data.</Typography>
          </Grid>
        ) : null}
      </Grid>
    </Container>
  );
}
