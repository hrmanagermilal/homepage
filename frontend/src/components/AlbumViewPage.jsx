import { useEffect, useMemo, useState } from "react";
import Box from "@mui/material/Box";
import ImageList from "@mui/material/ImageList";
import ImageListItem from "@mui/material/ImageListItem";
import Typography from "@mui/material/Typography";
import { api } from "../api/client";
import "./css/SubPage.css";
import "./css/AlbumViewPage.css";
import "./landing_components/css/Jubo.css";
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
  const [previewOpen, setPreviewOpen] = useState(false);
  const [previewImage, setPreviewImage] = useState("");
  const [previewAlt, setPreviewAlt] = useState("");

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

  useEffect(() => {
    if (!previewOpen) return;

    const onKeyDown = (event) => {
      if (event.key === "Escape") {
        setPreviewOpen(false);
      }
    };

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", onKeyDown);

    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [previewOpen]);

  const sortedImages = useMemo(() => {
    if (!Array.isArray(album?.images)) return [];
    return album.images.slice().sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
  }, [album]);

  console.log("AlbumViewPage - album:", album);

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#album");
    window.dispatchEvent(new Event("locationchange"));
  };

  const openPreview = (img, idx) => {
    const imageUrl = normalizeImageUrl(img.image_url);
    setPreviewImage(imageUrl);
    setPreviewAlt(img.alt_text || `${album?.title || "앨범"} ${idx + 1}`);
    setPreviewOpen(true);
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
                    <ImageList cols={1} gap={18} sx={{ m: 0 }} className="album-view__list">
                      {sortedImages.map((img, idx) => (
                        <ImageListItem
                          key={img.id ?? `${img.image_url}-${idx}`}
                          className="album-view__item"
                        >
                          <div
                            className="album-view__thumb-wrap"
                            role="button"
                            tabIndex={0}
                            aria-label="크게 보기"
                            onClick={() => openPreview(img, idx)}
                            onKeyDown={(e) => {
                              if (e.key === "Enter" || e.key === " ") {
                                e.preventDefault();
                                openPreview(img, idx);
                              }
                            }}
                          >
                            <img
                              src={normalizeImageUrl(img.image_url)}
                              alt={img.alt_text || `${album.title} ${idx + 1}`}
                              loading="lazy"
                              className="album-view__image"
                            />
                            <div className="main-weekly__hover-btn">
                              <img src="/images/main/icon-zoom.svg" alt="" />
                              <p>크게 보기</p>
                            </div>
                          </div>
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

      <div
        className={`weekly-popup${previewOpen ? " is-open" : ""}`}
        role="dialog"
        aria-modal="true"
        aria-label="앨범 크게 보기"
        onClick={() => setPreviewOpen(false)}
      >
        <button
          className="weekly-popup__close"
          type="button"
          aria-label="닫기"
          onClick={(e) => {
            e.stopPropagation();
            setPreviewOpen(false);
          }}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L15 15M15 1L1 15" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
          </svg>
        </button>
        {previewImage ? (
          <img
            className="weekly-popup__img"
            src={previewImage}
            alt={previewAlt}
            onClick={(e) => e.stopPropagation()}
          />
        ) : null}
      </div>
    </>
  );
}
