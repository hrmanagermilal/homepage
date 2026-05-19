# ─────────────────────────────────────────────
# Stage 1: Build the React application
# ─────────────────────────────────────────────
FROM node:20-alpine AS builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Bake VITE_ variables at build time.
# Pass --build-arg VITE_API_BASE_URL=https://api.milalchurch.com for direct
# cross-origin API access, or leave empty to rely on the nginx /api proxy below.
ARG VITE_API_BASE_URL=""
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

RUN npm run build

# ─────────────────────────────────────────────
# Stage 2: Serve with nginx
# ─────────────────────────────────────────────
FROM nginx:1.25-alpine

COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
