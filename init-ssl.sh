#!/bin/bash
# ============================================
# Let's Encrypt SSL Certificate Init Script
# Sets up certificates for frontend and/or backend
# ============================================

set -e

FRONTEND_DOMAIN="milalchurch.ca"
BACKEND_DOMAIN="milalchurch.ca"
EMAIL="it-team@milalchurch.com"
STAGING=0  # Set to 1 for testing (avoids rate limits)

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# Function to setup SSL for a service
setup_ssl() {
    local SERVICE=$1
    local DOMAIN=$2
    local SERVICE_DIR=$3
    
    echo ""
    echo "=========================================="
    echo "Setting up SSL for $SERVICE ($DOMAIN)"
    echo "=========================================="
    
    cd "$SERVICE_DIR"
    
    echo ">> Creating temporary self-signed certificate..."
    mkdir -p ./certs/live/$DOMAIN
    openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
      -keyout ./certs/live/$DOMAIN/privkey.pem \
      -out ./certs/live/$DOMAIN/fullchain.pem \
      -subj '/CN=localhost' 2>/dev/null || true
    
    echo ">> Certificate created at ./certs/live/$DOMAIN/"
    sleep 1
    
    # Verify temp cert exists
    if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
        echo -e "${RED}ERROR: Temporary certificate not created${NC}"
        return 1
    fi
    
    echo ">> Starting $SERVICE container..."
    docker compose up -d
    sleep 5
    
    echo ">> Requesting Let's Encrypt certificate for $DOMAIN..."
    
    STAGING_ARG=""
    if [ "$STAGING" -eq 1 ]; then
        STAGING_ARG="--staging"
    fi
    
    # Clean up any broken renewal config
    docker compose run --rm certbot sh -c "rm -f /etc/letsencrypt/renewal/$DOMAIN.conf" 2>/dev/null || true
    
    # Request real certificate (with wildcard for all subdomains)
    docker compose run --rm certbot certonly \
      --webroot \
      -w /var/www/certbot \
      -d "$DOMAIN" \
      -d "*.$DOMAIN" \
      --email "$EMAIL" \
      --agree-tos \
      --no-eff-email \
      --force-renewal \
      $STAGING_ARG
    
    # Verify real cert was obtained
    if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
        echo -e "${RED}ERROR: Certificate request failed for $DOMAIN${NC}"
        return 1
    fi
    
    echo ">> Reloading nginx with real certificate..."
    docker compose exec frontend nginx -s reload 2>/dev/null || \
    docker compose exec -w /var/www milal-nginx nginx -s reload 2>/dev/null || true
    
    echo -e "${GREEN}✓ SSL certificate installed for $DOMAIN${NC}"
    echo "  Location: ./certs/live/$DOMAIN/"
    echo "  - fullchain.pem"
    echo "  - privkey.pem"
}

# Main logic
usage() {
    echo "Usage: $0 [frontend|backend|all]"
    echo ""
    echo "Examples:"
    echo "  $0 frontend  # Setup SSL for frontend only (milalchurch.ca)"
    echo "  $0 backend   # Setup SSL for backend only (milalchurch.ca)"
    echo "  $0 all       # Setup SSL for both frontend and backend"
    echo ""
    echo "Default: all"
}

# Default to 'all' if no argument provided
TARGET="${1:-all}"

case "$TARGET" in
    frontend)
        setup_ssl "Frontend" "$FRONTEND_DOMAIN" "frontend"
        ;;
    backend)
        setup_ssl "Backend" "$BACKEND_DOMAIN" "backend"
        ;;
    all)
        setup_ssl "Frontend" "$FRONTEND_DOMAIN" "frontend"
        setup_ssl "Backend" "$BACKEND_DOMAIN" "backend"
        ;;
    *)
        echo "Invalid target: $TARGET"
        usage
        exit 1
        ;;
esac

echo ""
echo "=========================================="
echo "SSL Certificate Setup Complete!"
echo "=========================================="
echo ""
echo "Domains configured:"
echo "  • Frontend: $FRONTEND_DOMAIN"
echo "  • Backend:  $BACKEND_DOMAIN"
echo ""
echo "Next step: Start all services"
echo "  cd frontend && docker compose up -d"
echo "  cd backend && docker compose up -d"
echo ""
echo "To renew certificates manually:"
echo "  cd frontend && docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  cd backend && docker compose run --rm certbot renew --webroot -w /var/www/certbot"
