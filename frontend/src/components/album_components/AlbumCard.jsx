import Card from "@mui/material/Card";
import CardActionArea from "@mui/material/CardActionArea";
import CardContent from "@mui/material/CardContent";
import CardMedia from "@mui/material/CardMedia";
import Typography from "@mui/material/Typography";

export default function AlbumCard({
  id,
  title,
  description,
  date,
  image = "/images/sub/06-album/sub-visual-bg.jpg",
  href,
  onClick,
}) {
  const targetHref = href || `/news/album/${id}`;
  const isModalTrigger = typeof onClick === "function";

  const handleCardClick = (event) => {
    if (!isModalTrigger) return;
    event.preventDefault();
    onClick();
  };

  return (
    <li data-ani="top">
      <Card
        className="album-card-mui"
        sx={{
          width: "100%",
          height: "100%",
          borderRadius: "10rem",
          boxShadow: "0 10rem 7.5rem var(--op-b03)",
          overflow: "hidden",
        }}
      >
        <CardActionArea
          component={isModalTrigger ? "button" : "a"}
          href={isModalTrigger ? undefined : targetHref}
          type={isModalTrigger ? "button" : undefined}
          onClick={handleCardClick}
          sx={{
            height: "100%",
            display: "flex",
            alignItems: "stretch",
            flexDirection: "column",
          }}
        >
          <CardMedia
            component="img"
            height="220"
            image={image}
            alt={typeof title === "string" ? title.replace(/<br\s*\/?>/gi, " ") : "album image"}
            sx={{ objectFit: "cover" }}
          />
          <CardContent
            sx={{
              flexGrow: 1,
              display: "flex",
              flexDirection: "column",
              gap: 1,
              px: "18px",
              pt: "14px",
              pb: "16px",
            }}
          >
            <Typography
              gutterBottom
              variant="inherit"
              component="div"
              sx={{
                color: "var(--b-title)",
                fontSize: { xs: "20px", sm: "22px", lg: "24px" },
                fontWeight: 700,
                lineHeight: 1.35,
                letterSpacing: "-0.02em",
                display: "-webkit-box",
                WebkitLineClamp: 2,
                WebkitBoxOrient: "vertical",
                overflow: "hidden",
                textOverflow: "ellipsis",
                textAlign: "left",
                minHeight: "2.8em",
                mb: 0,
              }}
            >
              <span dangerouslySetInnerHTML={{ __html: title }} />
            </Typography>
            <Typography
              variant="inherit"
              sx={{
                color: "var(--b-default)",
                fontSize: { xs: "14px", sm: "15px", lg: "16px" },
                fontWeight: 400,
                flexGrow: 1,
                lineHeight: 1.65,
                display: "-webkit-box",
                WebkitLineClamp: 3,
                WebkitBoxOrient: "vertical",
                overflow: "hidden",
                textOverflow: "ellipsis",
                textAlign: "left",
              }}
            >
              {description}
            </Typography>
            <Typography
              variant="inherit"
              sx={{
                color: "var(--op-b30)",
                fontSize: { xs: "12px", sm: "13px" },
                fontWeight: 500,
                lineHeight: 1.4,
                mt: 0.5,
                textAlign: "left",
              }}
            >
              {date}
            </Typography>
          </CardContent>
        </CardActionArea>
      </Card>
    </li>
  );
}
