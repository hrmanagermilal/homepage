import { useMemo } from "react";
import "./css/IntroPastor.css";

function parseDbRow(row, lang) {
  const suffix = lang === "en" ? "en" : "ko";
  const parseParagraphs = (str) =>
    (str || "").split("\n\n").map((para) => para.split("\n").filter(Boolean));
  const parseList = (str) =>
    (str || "").split("\n").filter(Boolean);
  return {
    photoAlt:    row[`photo_alt_${suffix}`]     || "",
    titleLines:  [row[`title_line1_${suffix}`] || "", row[`title_line2_${suffix}`] || ""],
    paragraphs:  parseParagraphs(row[`paragraphs_${suffix}`]),
    pastorRole:  row[`pastor_role_${suffix}`]   || "",
    pastorName:  row[`pastor_name_${suffix}`]   || "",
    careerTitle: row[`career_title_${suffix}`]  || "",
    career:      parseList(row[`career_${suffix}`]),
  };
}

export default function IntroPastor({ pastorData = null, language = "kr" }) {
  const copy = useMemo(
    () => (pastorData ? parseDbRow(pastorData, language) : null),
    [pastorData, language]
  );

  if (!copy) return null;

  return (
    <section id="introduction02" className="intro-pastor">
      <div className="intro-pastor__inr">

        <div className="intro-pastor__photo" data-ani="left">
          <figure>
            <img src="/images/sub/01-introduction/pastor-photo.jpg" alt={copy.photoAlt} />
          </figure>
        </div>

        <div className="intro-pastor__cont">
          <h3 className="intro-pastor__title" data-heading="5xl" data-ani="right">
            {copy.titleLines[0]}<br />{copy.titleLines[1]}
          </h3>
          <div className="intro-pastor__texts" data-ani="right">
            {copy.paragraphs.map((lines, idx) => (
              <p className="intro-pastor__text" key={idx}>
                {lines.map((line, lineIdx) => (
                  <span key={lineIdx}>
                    {line}
                    {lineIdx < lines.length - 1 ? <br /> : null}
                  </span>
                ))}
              </p>
            ))}
          </div>
          <div className="intro-pastor__divider" aria-hidden="true" />
          <h4 className="intro-pastor__name" data-heading="2xl" data-ani="right">{copy.pastorRole} {copy.pastorName}</h4>
          <div className="intro-pastor__career" data-ani="right">
            <div className="intro-pastor__career-head">
              <i className="intro-pastor__career-logo" aria-hidden="true" />
              <p className="intro-pastor__career-title">{copy.careerTitle}</p>
            </div>
            <ul data-list="dot">
              {copy.career.map((item, idx) => <li key={idx}>{item}</li>)}
            </ul>
          </div>
        </div>

      </div>
    </section>
  );
}
