import { Chip, Divider, List, ListItem, ListItemText, Stack, Typography } from "@mui/material";

export default function DataList({ items, titleKey, subtitleKey, metaKey }) {
  if (!items?.length) {
    return (
      <Typography variant="body2" color="text.secondary">
        No data available.
      </Typography>
    );
  }

  return (
    <List disablePadding>
      {items.map((item, idx) => (
        <div key={item.id || idx}>
          <ListItem disableGutters sx={{ py: 1.3 }}>
            <ListItemText
              primary={item[titleKey] || "Untitled"}
              secondary={item[subtitleKey] || "-"}
              primaryTypographyProps={{ fontWeight: 700 }}
            />
            {metaKey && item[metaKey] ? (
              <Stack direction="row" spacing={1}>
                <Chip
                  size="small"
                  label={String(item[metaKey])}
                  color="secondary"
                  variant="outlined"
                />
              </Stack>
            ) : null}
          </ListItem>
          {idx < items.length - 1 ? <Divider /> : null}
        </div>
      ))}
    </List>
  );
}
