#!/bin/bash
# ============================================
# Let's Encrypt SSL Certificate Init Script
# Run this ONCE on first deployment.
# ============================================

set -e

DOMAIN="api.milalchurch.com"
EMAIL="dev@milalchurch.com"       # Change to your email
STAGING=0                         # Set to 1 for testing (avoids rate limits)

COMPOSE="docker compose"

echo "=== SSL Certificate Initialization ==="
echo "Domain: $DOMAIN"
echo "Email:  $EMAIL"
echo ""

# 1. Create a temporary self-signed cert so Nginx can start
echo ">> Creating temporary self-signed certificate..."
$COMPOSE run --rm --entrypoint "" certbot sh -c "
  mkdir -p /etc/letsencrypt/live/$DOMAIN
  openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
    -keyout /etc/letsencrypt/live/$DOMAIN/privkey.pem \
    -out /etc/letsencrypt/live/$DOMAIN/fullchain.pem \
    -subj '/CN=localhost'
"

# 2. Start Nginx with the dummy cert
echo ">> Starting Nginx..."
$COMPOSE up -d nginx

# 3. Remove the dummy cert
echo ">> Removing temporary certificate..."
$COMPOSE run --rm --entrypoint "" certbot sh -c "
  rm -rf /etc/letsencrypt/live/$DOMAIN
  rm -rf /etc/letsencrypt/archive/$DOMAIN
  rm -rf /etc/letsencrypt/renewal/$DOMAIN.conf
"

# 4. Request real certificate from Let's Encrypt
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

# 5. Reload Nginx with real cert
echo ">> Reloading Nginx..."
$COMPOSE exec nginx nginx -s reload

echo ""
echo "=== Done! SSL certificate installed for $DOMAIN ==="
echo "Run 'docker compose up -d' to start all services."
