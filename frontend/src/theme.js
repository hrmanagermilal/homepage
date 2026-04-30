import { createTheme } from "@mui/material/styles";

const theme = createTheme({
  palette: {
    mode: "light",
    primary: {
      main: "#0d5c63",
    },
    secondary: {
      main: "#ff7a59",
    },
    background: {
      default: "#f4f8f7",
      paper: "#ffffff",
    },
    text: {
      primary: "#122025",
      secondary: "#3e575c",
    },
  },
  shape: {
    borderRadius: 16,
  },
  typography: {
    fontFamily: "Manrope, Segoe UI, sans-serif",
    h3: {
      fontWeight: 800,
      letterSpacing: "-0.02em",
    },
    h5: {
      fontWeight: 700,
      letterSpacing: "-0.01em",
    },
    button: {
      textTransform: "none",
      fontWeight: 700,
    },
  },
  components: {
    MuiCard: {
      styleOverrides: {
        root: {
          backdropFilter: "blur(6px)",
          boxShadow: "0 14px 32px rgba(9, 35, 40, 0.10)",
        },
      },
    },
  },
});

export default theme;
