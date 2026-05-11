import { useMemo, useState } from "react";
import { Avatar, Box, Button, Card, CardContent, Container, Divider, Grid, Stack, Tab, Tabs, Typography } from "@mui/material";

const WORK_HISTORY = [
  "총신대학교 신학대학원(M.Div.)",
  "미주 한인교회 협력 선교위원",
  "전 뉴저지 한빛교회 담임목사",
  "현 밀알교회 담임목사",
];

const PEOPLE_TABS = [
  { label: "목사", category: "목회자" },
  { label: "장로", category: "장로" },
  { label: "간사", category: "간사" },
];

function resolveMemberCategory(member) {
  if (member?.category) {
    return member.category;
  }

  const title = String(member?.title || "");
  const role = String(member?.role || "").toLowerCase();

  if (title.includes("목사") || title.includes("전도사") || role.includes("pastor") || role.includes("evangelist")) {
    return "목회자";
  }
  if (title.includes("장로")) {
    return "장로";
  }
  return "간사";
}

const FALLBACK_TOGETHER_ITEMS = [
  {
    id: "fallback-1",
    title: "함께하는 교회",
    description: "지역과 열방을 위한 연합 사역 파트너",
    image: "/uploads/together/church-partner.jpg",
    link: "#",
  },
];

export default function IntroductionPage({ togetherItems = [], members = [], visionStatements = [] }) {
  const introTogetherItems = togetherItems.length ? togetherItems : FALLBACK_TOGETHER_ITEMS;
  const [peopleTab, setPeopleTab] = useState(0);
  const introVisionStatements = visionStatements
    .slice()
    .sort((a, b) => (a.order ?? 0) - (b.order ?? 0));

  const normalizedMembers = useMemo(
    () => members.map((member) => ({ ...member, category: resolveMemberCategory(member) })),
    [members]
  );

  const groupedMembers = useMemo(
    () =>
      PEOPLE_TABS.map((tab) => ({
        ...tab,
        members: normalizedMembers.filter((member) => member?.category === tab.category),
      })),
    [normalizedMembers]
  );

  const selectedGroup = groupedMembers[peopleTab] || groupedMembers[0];
  const seniorPastor =
    normalizedMembers.find((member) => member?.title === "담임목사") ||
    normalizedMembers.find((member) => member?.category === "목회자") ||
    null;

  return (
    <Box sx={{ py: { xs: 4, md: 7 } }}>
      <Container maxWidth="lg">
        <Box
          sx={{
            mb: 5,
            borderRadius: 3,
            overflow: "hidden",
            minHeight: { xs: 280, md: 380 },
            position: "relative",
            display: "flex",
            alignItems: "flex-end",
            backgroundImage: "linear-gradient(180deg, rgba(5,26,32,0.1) 0%, rgba(5,26,32,0.72) 100%), url('/uploads/hero/background/hero_bg_1.jpg')",
            backgroundSize: "cover",
            backgroundPosition: "center",
          }}
        >
          <Box sx={{ p: { xs: 2.5, md: 4 }, color: "white", maxWidth: 760 }}>
            <Typography variant="overline" sx={{ letterSpacing: 2.4, opacity: 0.88 }}>
              INTRODUCTION
            </Typography>
            <Typography variant="h3" sx={{ fontWeight: 800, lineHeight: 1.15, mt: 0.6 }}>
              하나님의 사랑으로
              <br />
              세대를 잇는 공동체, 밀알교회
            </Typography>
            <Typography variant="body1" sx={{ mt: 1.4, opacity: 0.92 }}>
              예배와 말씀, 기도와 결단으로 이어지는 건강한 신앙공동체를 지향합니다.
            </Typography>
          </Box>
        </Box>

        <Card sx={{ borderRadius: 3, mb: 5 }}>
          <CardContent sx={{ p: { xs: 2.5, md: 4 } }}>
            <Typography variant="h4" sx={{ fontWeight: 800, mb: 2.5 }}>
              담임목사 인사말
            </Typography>
            <Grid container spacing={3} alignItems="flex-start">
              <Grid item xs={12} md={4}>
                <Stack spacing={1.2} alignItems={{ xs: "center", md: "flex-start" }}>
                  <Avatar
                    src={seniorPastor?.picture || "/uploads/members/pastor_jb.jpg"}
                    alt="담임목사"
                    sx={{ width: 200, height: 200, border: "3px solid rgba(13,92,99,0.22)" }}
                  />
                  <Typography variant="h6" sx={{ fontWeight: 700 }}>
                    {seniorPastor ? `${seniorPastor.name} ${seniorPastor.title || ""}`.trim() : "박진범 담임목사"}
                  </Typography>
                </Stack>
              </Grid>
              <Grid item xs={12} md={8}>
                <Typography variant="body1" color="text.secondary" sx={{ mb: 2.2 }}>
                  밀알교회는 복음의 본질 위에서 시대를 품는 교회가 되기를 소망합니다. 한 사람의 변화가 가정과
                  세상을 새롭게 한다는 믿음으로, 예배와 양육, 선교의 현장을 성실하게 세워가겠습니다.
                </Typography>
                <Divider sx={{ mb: 1.8 }} />
                <Typography variant="subtitle1" sx={{ fontWeight: 700, mb: 1.2 }}>
                  사역 이력
                </Typography>
                <Stack spacing={1}>
                  {WORK_HISTORY.map((item) => (
                    <Typography key={item} variant="body2" color="text.secondary">
                      • {item}
                    </Typography>
                  ))}
                </Stack>
              </Grid>
            </Grid>
          </CardContent>
        </Card>

        <Box sx={{ mb: 5 }}>
          <Typography variant="h4" sx={{ fontWeight: 800, mb: 2.2 }}>
            Vision
          </Typography>
          {introVisionStatements.length ? (
            <Grid container spacing={2.5}>
              {introVisionStatements.map((statement) => {
                const points = String(statement.points || "")
                  .split("\n")
                  .map((line) => line.trim())
                  .filter(Boolean);

                return (
                  <Grid item xs={12} md={6} key={statement.id || statement.title}>
                    <Card sx={{ height: "100%", borderRadius: 3, bgcolor: "rgba(13,92,99,0.06)" }}>
                      <CardContent sx={{ p: { xs: 2.2, md: 3 } }}>
                        <Typography variant="h6" sx={{ fontWeight: 800, mb: 1.4 }}>
                          {statement.title}
                        </Typography>
                        <Stack spacing={0.8}>
                          {(points.length ? points : ["내용이 없습니다."]).map((line, idx) => (
                            <Typography key={`${statement.id || statement.title}-point-${idx}`} variant="body2" color="text.secondary">
                              • {line}
                            </Typography>
                          ))}
                        </Stack>
                      </CardContent>
                    </Card>
                  </Grid>
                );
              })}
            </Grid>
          ) : (
            <Card sx={{ borderRadius: 3, bgcolor: "rgba(13,92,99,0.06)" }}>
              <CardContent>
                <Typography variant="body2" color="text.secondary">
                  비전 데이터가 없습니다. `/api/vision-statements` 결과를 확인해 주세요.
                </Typography>
              </CardContent>
            </Card>
          )}
        </Box>

        <Box sx={{ mb: 5 }}>
          <Typography variant="h4" sx={{ fontWeight: 800, mb: 2.2 }}>
            People
          </Typography>
          <Card sx={{ borderRadius: 3 }}>
            <CardContent>
              <Tabs
                value={peopleTab}
                onChange={(_, value) => setPeopleTab(value)}
                variant="scrollable"
                scrollButtons="auto"
                sx={{ mb: 2 }}
              >
                {PEOPLE_TABS.map((tab) => (
                  <Tab key={tab.label} label={tab.label} />
                ))}
              </Tabs>

              <Typography variant="h6" sx={{ fontWeight: 800, mb: 1.3 }}>
                {selectedGroup?.label}
              </Typography>
              {selectedGroup?.members?.length ? (
                <Grid container spacing={1.5}>
                  {selectedGroup.members.map((person, idx) => (
                    <Grid item xs={12} sm={6} md={4} key={`${selectedGroup?.category || "people"}-${person.id || person.name}-${idx}`}>
                      <Card variant="outlined" sx={{ borderRadius: 2 }}>
                        <CardContent sx={{ p: 1.5 }}>
                          <Stack direction="row" spacing={1.2} alignItems="center" sx={{ mb: 1 }}>
                            <Avatar src={person.picture || ""} alt={person.name || "member"} sx={{ width: 40, height: 40 }} />
                            <Box>
                              <Typography variant="subtitle2" sx={{ fontWeight: 700, lineHeight: 1.2 }}>
                                {person.name}
                              </Typography>
                              <Typography variant="caption" color="text.secondary">
                                {person.title || person.role || "사역자"}
                              </Typography>
                            </Box>
                          </Stack>
                          {person.email ? (
                            <Typography variant="caption" color="text.secondary">
                              {person.email}
                            </Typography>
                          ) : null}
                        </CardContent>
                      </Card>
                    </Grid>
                  ))}
                </Grid>
              ) : (
                <Typography variant="body2" color="text.secondary">
                  등록된 인원이 없습니다.
                </Typography>
              )}
            </CardContent>
          </Card>
        </Box>

        <Box sx={{ mb: 5 }}>
          <Typography variant="h4" sx={{ fontWeight: 800, mb: 2.2 }}>
            함께하는 교회
          </Typography>
          <Grid container spacing={2.5}>
            {introTogetherItems.map((item) => (
              <Grid item xs={12} md={4} key={item.id}>
                <Card sx={{ height: "100%", borderRadius: 3, overflow: "hidden" }}>
                  <Box
                    component="img"
                    src={item.image || "/uploads/together/church-partner.jpg"}
                    alt={item.title || "함께하는 교회"}
                    sx={{ width: "100%", height: 180, objectFit: "cover" }}
                  />
                  <CardContent>
                    <Typography variant="h6" sx={{ fontWeight: 800, mb: 0.8 }}>
                      {item.title || "함께하는 교회"}
                    </Typography>
                    <Typography variant="body2" color="text.secondary" sx={{ mb: 1.5 }}>
                      {item.description || "밀알교회와 함께 사역하는 교회를 소개합니다."}
                    </Typography>
                    <Button
                      variant="outlined"
                      size="small"
                      href={item.link || "#"}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      교회 보기
                    </Button>
                  </CardContent>
                </Card>
              </Grid>
            ))}
          </Grid>
        </Box>

        <Stack direction={{ xs: "column", sm: "row" }} spacing={1.2}>
          <Button variant="contained" sx={{ px: 3 }} href="/">
            메인으로 돌아가기
          </Button>
          <Button variant="outlined" sx={{ px: 3 }} href="/" onClick={() => sessionStorage.setItem("goToContacts", "1")}>
            교회 연락처 보기
          </Button>
        </Stack>
      </Container>
    </Box>
  );
}
