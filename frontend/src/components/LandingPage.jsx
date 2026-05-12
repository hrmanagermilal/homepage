import Hero from "./Hero";
import Sermon from "./Sermon";
import ServiceTime from "./ServiceTime";
import Jubo from "./Jubo";
import Announcement from "./Announcement";
import Contacts from "./Contacts";
import FooterTop from "./FooterTop";

export default function LandingPage({
  hero,
  quickLinks,
  sermons,
  departments,
  bulletins,
  announcements,
  sections,
  togetherItems,
}) {
  return (
    <>
      <Hero hero={hero} quickLinks={quickLinks} />
      <Sermon items={sermons} section={sections.find((s) => s.title === "최신 설교")} />
      <ServiceTime departments={departments} section={sections.find((s) => s.title === "예배 시간")} />
      <Jubo items={bulletins} section={sections.find((s) => s.title === "주보")} />
      <Announcement items={announcements} section={sections.find((s) => s.title === "공지사항")} />
      <Contacts section={sections.find((s) => s.title === "오시는 길")} />
      <FooterTop items={togetherItems} section={sections.find((s) => s.title === "함께하는 교회")} />
    </>
  );
}
