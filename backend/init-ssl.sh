#!/bin/bash
# ============================================
# Let's Encrypt SSL Certificate Init Script
# Run this ONCE on first deployment.
# ============================================

set -e

DOMAIN="milalchurch.ca"
EMAIL="it-team@milalchurch.com"       # Change to your email
STAGING=0                         # Set to 1 for testing (avoids rate limits)

COMPOSE="docker compose"

echo "=== SSL Certificate Initialization ==="
echo "Domain: $DOMAIN"
echo "Email:  $EMAIL"
echo ""

# 1. Create a temporary self-signed cert so Nginx can start
echo ">> Creating temporary self-signed certificate..."
mkdir -p ./certs/live/$DOMAIN
openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
  -keyout ./certs/live/$DOMAIN/privkey.pem \
  -out ./certs/live/$DOMAIN/fullchain.pem \
  -subj '/CN=localhost' 2>/dev/null || true

echo ">> Temporary certificate created at ./certs/live/$DOMAIN/"
sleep 1

# Verify temporary cert was created
if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
  echo "ERROR: Temporary certificate not found at ./certs/live/$DOMAIN/fullchain.pem"
  exit 1
fi

# 2. Start Nginx with the dummy cert
echo ">> Starting Nginx..."
$COMPOSE up -d nginx

# Wait for container to be ready
echo ">> Waiting for Nginx to be ready..."
sleep 5

# 3. Request real certificate from Let's Encrypt
echo ">> Requesting Let's Encrypt certificate..."

STAGING_ARG=""
if [ "$STAGING" -eq 1 ]; then
  STAGING_ARG="--staging"
fi

# Clean up any broken renewal config from the temporary cert
$COMPOSE run --rm certbot sh -c "rm -f /etc/letsencrypt/renewal/$DOMAIN.conf" 2>/dev/null || true

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

# 5. Reload Nginx with real cert
echo ">> Reloading Nginx..."
$COMPOSE exec nginx nginx -s reload

echo ""
echo "=== Done! SSL certificate installed for $DOMAIN ==="
echo ""
echo "Certificate location: ./certs/live/$DOMAIN/"
echo "  - fullchain.pem (used by nginx)"
echo "  - privkey.pem (used by nginx)"
echo ""
echo "To renew the certificate manually, run:"
echo "  docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  docker compose exec nginx nginx -s reload"
