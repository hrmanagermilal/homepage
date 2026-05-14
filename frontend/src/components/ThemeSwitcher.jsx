import { useState, useEffect } from "react";

const THEMES = [
  { id: "dark-green", label: "Dark Green", swatch: "#3a472b" },
  { id: "dark-blue",  label: "Dark Blue",  swatch: "#1a2c4e" },
  { id: "dark-red",   label: "Dark Red",   swatch: "#4a1a1a" },
  { id: "gold",        label: "Gold",         swatch: "#5a3e10" },
  { id: "dark-orange", label: "Dark Orange",  swatch: "#6b2d0f" },
];

const STORAGE_KEY = "milal-theme";
const DEFAULT_THEME = "dark-green";

export function useTheme() {
  const [theme, setThemeState] = useState(() => {
    return localStorage.getItem(STORAGE_KEY) || DEFAULT_THEME;
  });

  useEffect(() => {
    document.documentElement.setAttribute("data-theme", theme);
    localStorage.setItem(STORAGE_KEY, theme);
  }, [theme]);

  return [theme, setThemeState];
}

export default function ThemeSwitcher({ theme, setTheme }) {
  const [open, setOpen] = useState(false);
  const current = THEMES.find((t) => t.id === theme) || THEMES[0];

  return (
    <div className="theme-switcher" aria-label="테마 선택">
      <button
        className="theme-switcher__trigger"
        type="button"
        aria-label={`현재 테마: ${current.label}. 테마 변경`}
        aria-expanded={open}
        onClick={() => setOpen((v) => !v)}
      >
        <span
          className="theme-switcher__swatch"
          style={{ background: current.swatch }}
          aria-hidden="true"
        />
      </button>

      {open && (
        <>
          <div
            className="theme-switcher__backdrop"
            onClick={() => setOpen(false)}
            aria-hidden="true"
          />
          <ul className="theme-switcher__panel" role="listbox" aria-label="테마 목록">
            {THEMES.map((t) => (
              <li key={t.id} role="option" aria-selected={t.id === theme}>
                <button
                  type="button"
                  className={`theme-switcher__option${t.id === theme ? " is-active" : ""}`}
                  onClick={() => { setTheme(t.id); setOpen(false); }}
                >
                  <span
                    className="theme-switcher__swatch"
                    style={{ background: t.swatch }}
                    aria-hidden="true"
                  />
                  <span className="theme-switcher__option-label">{t.label}</span>
                </button>
              </li>
            ))}
          </ul>
        </>
      )}
    </div>
  );
}
