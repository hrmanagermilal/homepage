import { Card, CardContent, Stack, Typography } from "@mui/material";

export default function SectionCard({ title, subtitle, children, delay = 0, apiItems = [] }) {
  return (
    <Card className="fade-up" sx={{ animationDelay: `${delay}ms`, borderRadius: 4 }}>
      <CardContent>
        <Stack spacing={1.5}>
          <Typography variant="h5">{title}</Typography>
          {subtitle ? (
            <Typography variant="body2" color="text.secondary">
              {subtitle}
            </Typography>
          ) : null}
          {apiItems.length ? (
            <Stack spacing={0.5}>
              {apiItems.map((item, index) => (
                <Typography key={`${item}-${index}`} variant="caption" color="text.secondary">
                  {item}
                </Typography>
              ))}
            </Stack>
          ) : null}
          {children}
        </Stack>
      </CardContent>
    </Card>
  );
}
