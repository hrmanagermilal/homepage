#!/bin/bash
# ============================================
# Let's Encrypt SSL Certificate Init Script for Frontend
# Run this ONCE on first deployment for milalchurch.ca
# ============================================

set -e

DOMAIN="milalchurch.ca"
EMAIL="it-team@milalchurch.com"       # Change to your email
STAGING=0                         # Set to 1 for testing (avoids rate limits)

COMPOSE="docker compose"

echo "=== SSL Certificate Initialization for Frontend ==="
echo "Domain: $DOMAIN"
echo "Email:  $EMAIL"
echo ""

# Create directories if they don't exist
mkdir -p ./certs
mkdir -p ./certbot/www

# 1. Create a temporary self-signed cert so Nginx can start
echo ">> Creating temporary self-signed certificate..."
$COMPOSE run --rm --entrypoint "" certbot sh -c "
  mkdir -p /etc/letsencrypt/live/$DOMAIN
  openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
    -keyout /etc/letsencrypt/live/$DOMAIN/privkey.pem \
    -out /etc/letsencrypt/live/$DOMAIN/fullchain.pem \
    -subj '/CN=localhost'
"

# Copy the self-signed cert to the local certs directory
# (docker compose run mounted volumes make these accessible)
echo ">> Copying temporary certificate to ./certs..."
# Wait a moment for files to be available
sleep 1

# Verify temporary cert was created
if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
  echo "ERROR: Temporary certificate not found at ./certs/live/$DOMAIN/fullchain.pem"
  exit 1
fi

# 2. Start frontend with the dummy cert
echo ">> Starting frontend container..."
$COMPOSE up -d frontend

# Wait for container to be ready
echo ">> Waiting for frontend to be ready..."
sleep 5

# 3. Request real certificate from Let's Encrypt
echo ">> Requesting Let's Encrypt certificate..."

STAGING_ARG=""
if [ "$STAGING" -eq 1 ]; then
  STAGING_ARG="--staging"
fi

$COMPOSE run --rm certbot certonly \
  --webroot \
  -w /var/www/certbot \
  -d "$DOMAIN" \
  --email "$EMAIL" \
  --agree-tos \
  --no-eff-email \
  --force-renewal \
  $STAGING_ARG

# 4. Verify real certificate was obtained
if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
  echo "ERROR: Real certificate not found at ./certs/live/$DOMAIN/fullchain.pem"
  echo "Certificate request may have failed. Check the output above."
  exit 1
fi

echo ">> Real certificate obtained successfully"

# 5. Reload frontend nginx with real cert
echo ">> Reloading frontend nginx..."
$COMPOSE exec frontend nginx -s reload

echo ""
echo "=== Done! SSL certificate installed for $DOMAIN ==="
echo ""
echo "Certificate location: ./certs/live/$DOMAIN/"
echo "  - fullchain.pem (used by nginx)"
echo "  - privkey.pem (used by nginx)"
echo ""
echo "To renew the certificate manually, run:"
echo "  docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  docker compose exec frontend nginx -s reload"


