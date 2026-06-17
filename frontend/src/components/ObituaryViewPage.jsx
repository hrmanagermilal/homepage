import { useEffect, useState } from "react";
import "./css/SubPage.css";
import "./css/ObituaryViewPage.css";
import ObituaryViewSubVisual from "./obituary_components/ObituaryViewSubVisual";
import ObituaryViewContent from "./obituary_components/ObituaryViewContent";
import ObituaryViewNavigation from "./obituary_components/ObituaryViewNavigation";
import { api } from "../api/client";

function getObituaryIdFromPath() {
  const match = window.location.pathname.match(/\/news\/obituary\/(\d+)/);
  return match ? Number(match[1]) : null;
}

export default function ObituaryViewPage() {
  const [obituary, setObituary] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchObituary = () => {
      const obituaryId = getObituaryIdFromPath();
      console.log("Fetching obituary with ID:", obituaryId);
      if (obituaryId) {
        setLoading(true);
        setObituary(null);
        api.getObituaryById(obituaryId)
          .then((response) => {
            console.log("Obituary fetch response:", response);
            
              setObituary(response);

          })
          .catch((err) => console.error("Failed to fetch obituary:", err))
          .finally(() => setLoading(false));
      }
    };

    fetchObituary();

    window.addEventListener("locationchange", fetchObituary);
    return () => window.removeEventListener("locationchange", fetchObituary);
  }, []);

  useEffect(() => {
    const el = document.getElementById("content");
    if (el) {
      const header = document.querySelector(".site-header");
      const headerHeight = header ? header.offsetHeight + header.offsetTop : 0;
      window.scrollTo({ top: el.offsetTop - headerHeight - 16, behavior: "smooth" });
    }
  }, [obituary]);

  const handleListClick = () => {
    window.history.pushState({}, "", "/news#obituary");
    window.dispatchEvent(new Event("locationchange"));
  };

  if (loading || !obituary) {
    return (
      <>
        <ObituaryViewSubVisual />
        <div className="sub-content" id="content">
          <section className="obituary board-view">
            <div className="wrap-narrow">
              <p>{loading ? "부고 내용을 불러오는 중입니다..." : "부고 내용을 찾을 수 없습니다."}</p>
            </div>
          </section>
        </div>
      </>
    );
  }

  return (
    <>
      <ObituaryViewSubVisual />
      <div className="sub-content" id="content">
        <section className="obituary board-view">
          <div className="wrap-narrow">
            <ObituaryViewContent title={obituary.title} content={obituary.content} />
            <ObituaryViewNavigation onListClick={handleListClick} />
          </div>
        </section>
      </div>
    </>
  );
}
