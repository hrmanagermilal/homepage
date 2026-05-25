import { useState, useEffect, useCallback } from "react";
import { api } from "../api/client";

const THEMES = [
  { id: "dark-green", label: "Green", swatch: "#3a472b" },
  { id: "dark-blue",  label: "Blue",  swatch: "#1a2c4e" },
  { id: "dark-brown", label: "Brown", swatch: "#39221C" },
];

const STORAGE_KEY = "milal-theme";
const DEFAULT_THEME = "dark-green";

export function useTheme() {
  const [theme, setThemeState] = useState(() => {
    return localStorage.getItem(STORAGE_KEY) || DEFAULT_THEME;
  });

  // Apply theme to DOM and persist locally
  useEffect(() => {
    document.documentElement.setAttribute("data-theme", theme);
    localStorage.setItem(STORAGE_KEY, theme);
  }, [theme]);

  // On mount: load the DB-stored theme and apply it as the authoritative default
  useEffect(() => {
    api.getTheme()
      .then((json) => {
        console.log("Fetched theme from API:", json);
        const remote = json?.data?.theme;
        if (remote && remote !== (localStorage.getItem(STORAGE_KEY) || DEFAULT_THEME)) {
          setThemeState(remote);
        }
      })
      .catch(() => {});
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const setTheme = useCallback((newTheme) => {
    setThemeState(newTheme);
    api.setTheme(newTheme).catch(() => {});
  }, []);

  console.log("Current theme:", theme);

  return [theme, setTheme];
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
