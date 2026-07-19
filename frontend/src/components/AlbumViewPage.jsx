import { useEffect, useMemo, useState } from "react";
import Box from "@mui/material/Box";
import ImageList from "@mui/material/ImageList";
import ImageListItem from "@mui/material/ImageListItem";
import Typography from "@mui/material/Typography";
import { api } from "../api/client";
import "./css/SubPage.css";
import "./css/AlbumViewPage.css";
import AlbumViewSubVisual from "./album_components/AlbumViewSubVisual";
import AlbumViewContent from "./album_components/AlbumViewContent";
import AlbumViewNavigation from "./album_components/AlbumViewNavigation";

function getAlbumIdFromPath() {
  const match = window.location.pathname.match(/\/news\/album\/(\d+)/);
  return match ? Number(match[1]) : null;
}

function normalizeImageUrl(url) {
  if (!url) return "";
  return url.startsWith("/") ? url : `/${url}`;
}

export default function AlbumViewPage() {
  const [album, setAlbum] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchAlbum = () => {
      const albumId = getAlbumIdFromPath();
      if (!albumId) return;

      setLoading(true);
      setAlbum(null);
      api
        .getAlbumById(albumId)
        .then((response) => {
          const data = response?.data ?? response;
          setAlbum(data ?? null);
        })
        .catch(() => setAlbum(null))
        .finally(() => setLoading(false));
    };

    fetchAlbum();
    window.addEventListener("locationchange", fetchAlbum);
    return () => window.removeEventListener("locationchange", fetchAlbum);
  }, []);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [album]);

  const sortedImages = useMemo(() => {
    if (!Array.isArray(album?.images)) return [];
    return album.images.slice().sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
  }, [album]);

  console.log("AlbumViewPage - album:", album);

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#album");
    window.dispatchEvent(new Event("locationchange"));
  };

  return (
    <>
      <AlbumViewSubVisual />
      <div className="sub-content" id="content">
        <section className="album board-view">
          <div className="wrap-narrow">
            {loading || !album ? (
              <p>{loading ? "앨범 내용을 불러오는 중입니다..." : "앨범 내용을 찾을 수 없습니다."}</p>
            ) : (
              <>
                <AlbumViewContent title={album.title} content={album.content || album.description || ""} />

                {sortedImages.length > 0 ? (
                  <Box sx={{ mt: 4 }}>
                    <ImageList cols={1} gap={18} sx={{ m: 0 }}>
                      {sortedImages.map((img, idx) => (
                        <ImageListItem
                          key={img.id ?? `${img.image_url}-${idx}`}
                          sx={{
                            borderRadius: "10px",
                            overflow: "hidden",
                            backgroundColor: "#f2f2f2",
                            display: "flex",
                            justifyContent: "center",
                          }}
                        >
                          <img
                            src={normalizeImageUrl(img.image_url)}
                            alt={img.alt_text || `${album.title} ${idx + 1}`}
                            loading="lazy"
                            style={{
                              width: "100%",
                              maxWidth: "600px",
                              height: "400px",
                              objectFit: "cover",
                              display: "block",
                              margin: "0 auto",
                            }}
                          />
                        </ImageListItem>
                      ))}
                    </ImageList>
                  </Box>
                ) : (
                  <Typography sx={{ mt: 3, color: "var(--op-b50)" }}>등록된 앨범 이미지가 없습니다.</Typography>
                )}
              </>
            )}

            <AlbumViewNavigation onListClick={handleListClick} />
          </div>
        </section>
      </div>
    </>
  );
}
