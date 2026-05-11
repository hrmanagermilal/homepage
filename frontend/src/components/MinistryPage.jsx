import { Box, Button, Container, Stack, Typography } from "@mui/material";

export default function MinistryPage() {
  return (
    <Box sx={{ py: { xs: 8, md: 12 } }}>
      <Container maxWidth="md">
        <Stack spacing={2} alignItems="center" textAlign="center">
          <Typography variant="overline" sx={{ letterSpacing: 2, color: "text.secondary" }}>
            MINISTRY
          </Typography>
          <Typography variant="h3" sx={{ fontWeight: 800 }}>
            사역
          </Typography>
          <Typography variant="body1" color="text.secondary" sx={{ maxWidth: 560 }}>
            이 페이지는 준비 중입니다. 곧 사역 소개와 부서별 사역 내용을 제공할 예정입니다.
          </Typography>
          <Button variant="contained" href="/">
            홈으로 이동
          </Button>
        </Stack>
      </Container>
    </Box>
  );
}
