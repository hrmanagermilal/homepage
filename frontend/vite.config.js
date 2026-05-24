import { defineConfig, loadEnv } from "vite";
import react from "@vitejs/plugin-react";

// Rewrite /images/* → /uploads/* in the dev server so both paths work
// (mirrors the nginx alias used in production).
const imagesAlias = {
  name: "images-alias",
  configureServer(server) {
    server.middlewares.use((req, _res, next) => {
      if (req.url?.startsWith("/images/")) {
        req.url = req.url.replace(/^\/images\//, "/uploads/");
      }
      next();
    });
  },
};

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const proxyTarget = env.VITE_API_PROXY_TARGET || "http://localhost";
  const envAllowedHosts = (env.VITE_ALLOWED_HOSTS || "")
    .split(",")
    .map((host) => host.trim())
    .filter(Boolean);
  const allowedHosts = [
    "ec2-13-58-1-108.us-east-2.compute.amazonaws.com",
    ...envAllowedHosts,
  ];

  return {
    plugins: [react(), imagesAlias],
    server: {
      port: 3000,
      host: true,
      allowedHosts,
      proxy: {
        "/api": {
          target: proxyTarget,
          changeOrigin: true,
          secure: false,
        },
      },
    },
  };
});
