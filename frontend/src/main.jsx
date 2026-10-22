import "@fontsource/manrope/400.css";
import "@fontsource/manrope/600.css";
import "@fontsource/manrope/700.css";
import "@fontsource/manrope/800.css";
import React from "react";
import ReactDOM from "react-dom/client";
import { ThemeProvider } from "@mui/material";
import App from "./App";
import JuboLatestPage from "./components/JuboLatestPage";
import theme from "./theme";

ReactDOM.createRoot(document.getElementById("root")).render(
  <React.StrictMode>
    <ThemeProvider theme={theme}>
      {/^\/jubo-latest\/?$/.test(window.location.pathname) ? <JuboLatestPage /> : <App />}
    </ThemeProvider>
  </React.StrictMode>
);
