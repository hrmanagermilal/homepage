import { Card, CardContent, Container, Grid2 as Grid, Stack, Typography } from "@mui/material";

export default function Contacts({ members = [] }) {
  console.log("Contacts members:", members);
  const contacts = members.map((member) => ({
    id: member.id,
    title: member.name || member.title || "Member",
    subtitle: member.role || member.title || "",
    value: member.email || "No email",
  }));

  return (
    <Container id="contacts" maxWidth="lg" sx={{ py: 2 }}>
      <Typography variant="h4" sx={{ mb: 2, fontWeight: 800 }}>
        Contacts
      </Typography>
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
