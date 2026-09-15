#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FRONTEND_COMPOSE="$ROOT_DIR/frontend/docker-compose.yml"
BACKEND_COMPOSE="$ROOT_DIR/backend/docker-compose.yml"
CERTS_DIR="$ROOT_DIR/frontend/certs"
LIVE_DIR="$CERTS_DIR/live"
NGINX_LINEAGE="$LIVE_DIR/milalchurch.ca"
BROKEN_RENEWAL_CONFIG="$CERTS_DIR/renewal/milalchurch.ca.conf"
LOCK_FILE="/run/lock/milalchurch-certbot-renew.lock"

if [ "$EUID" -ne 0 ]; then
    echo "ERROR: Run this script as root: sudo $0" >&2
    exit 1
fi

exec 9>"$LOCK_FILE"
if ! flock -n 9; then
    echo "Another certificate renewal is already running."
    exit 0
fi

if [ -f "$BROKEN_RENEWAL_CONFIG" ] &&
    ! grep -q '^fullchain = ' "$BROKEN_RENEWAL_CONFIG"; then
    DISABLED_CONFIG="${BROKEN_RENEWAL_CONFIG%.conf}.disabled-$(date +%Y%m%d%H%M%S)"
    echo ">> Disabling broken renewal configuration: $BROKEN_RENEWAL_CONFIG"
    mv "$BROKEN_RENEWAL_CONFIG" "$DISABLED_CONFIG"
fi

echo ">> Renewing Let's Encrypt certificates..."
docker compose -f "$FRONTEND_COMPOSE" run --rm certbot renew \
    --webroot \
    -w /var/www/certbot \
    --non-interactive

LATEST_CERTIFICATE=""
LATEST_EXPIRY=0
for CERTIFICATE in "$LIVE_DIR"/milalchurch.ca*/fullchain.pem; do
    [ -f "$CERTIFICATE" ] || continue
    EXPIRY_TEXT="$(openssl x509 -in "$CERTIFICATE" -noout -enddate | cut -d= -f2-)"
    EXPIRY_EPOCH="$(date -d "$EXPIRY_TEXT" +%s)"
    if [ "$EXPIRY_EPOCH" -gt "$LATEST_EXPIRY" ]; then
        LATEST_CERTIFICATE="$CERTIFICATE"
        LATEST_EXPIRY="$EXPIRY_EPOCH"
    fi
done

if [ -z "$LATEST_CERTIFICATE" ] ||
    ! openssl x509 -in "$LATEST_CERTIFICATE" -noout -checkend 1209600; then
    echo "ERROR: Certificate is missing, expired, or valid for fewer than 14 days." >&2
    exit 1
fi

ACTIVE_LINEAGE="$(dirname "$LATEST_CERTIFICATE")"
if [ "$ACTIVE_LINEAGE" != "$NGINX_LINEAGE" ]; then
    echo ">> Pointing nginx to the newest lineage: $(basename "$ACTIVE_LINEAGE")"
    mkdir -p "$NGINX_LINEAGE"
    ln -sfn "../$(basename "$ACTIVE_LINEAGE")/fullchain.pem" "$NGINX_LINEAGE/fullchain.pem"
    ln -sfn "../$(basename "$ACTIVE_LINEAGE")/privkey.pem" "$NGINX_LINEAGE/privkey.pem"
fi

echo ">> Reloading frontend nginx..."
docker compose -f "$FRONTEND_COMPOSE" exec -T frontend nginx -t
docker compose -f "$FRONTEND_COMPOSE" exec -T frontend nginx -s reload

if [ -n "$(docker compose -f "$BACKEND_COMPOSE" ps --status running -q nginx)" ]; then
    echo ">> Reloading backend nginx..."
    docker compose -f "$BACKEND_COMPOSE" exec -T nginx nginx -t
    docker compose -f "$BACKEND_COMPOSE" exec -T nginx nginx -s reload
fi

echo ">> Installed certificate:"
openssl x509 -in "$LATEST_CERTIFICATE" -noout -subject -issuer -dates -serial