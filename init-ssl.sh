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
    
    # Wait for nginx to be ready (max 30 seconds)
    echo ">> Waiting for nginx to be ready..."
    max_attempts=30
    attempt=0
    while [ $attempt -lt $max_attempts ]; do
        if docker compose exec -T milal-frontend nginx -t 2>/dev/null || docker compose exec -T milal-nginx nginx -t 2>/dev/null; then
            echo ">> nginx is ready"
            break
        fi
        attempt=$((attempt + 1))
        sleep 1
    done
    
    if [ $attempt -eq $max_attempts ]; then
        echo -e "${RED}WARNING: nginx did not become ready in time, proceeding anyway...${NC}"
    fi
    
    sleep 2
    
    echo ">> Requesting Let's Encrypt certificate for $DOMAIN..."
    
    STAGING_ARG=""
    if [ "$STAGING" -eq 1 ]; then
        STAGING_ARG="--staging"
    fi
    
    # Clean up any broken renewal config (for fresh certificate request)
    # Using separate docker compose run commands to avoid syntax issues
    docker compose run --rm certbot rm -f /etc/letsencrypt/renewal/$DOMAIN.conf 2>/dev/null || true
    
    # Verify certbot can access the webroot
    echo ">> Verifying webroot accessibility..."
    docker compose run --rm certbot test -d /var/www/certbot && echo ">> Webroot is accessible" || echo ">> WARNING: Webroot may not be accessible"
    
    # Request real certificate (with wildcard for all subdomains)
    # NOTE: Don't use --force-renewal by default (causes rate limit issues)
    # Use 'renew' for existing certs or 'certonly' for new requests
    if ! docker compose run --rm certbot certonly \
      --webroot \
      -w /var/www/certbot \
      -d "$DOMAIN" \
      -d "*.$DOMAIN" \
      --email "$EMAIL" \
      --agree-tos \
      --no-eff-email \
      --non-interactive \
      $STAGING_ARG; then
        echo -e "${RED}ERROR: Certificate request failed for $DOMAIN${NC}"
        echo ""
        echo "Troubleshooting steps:"
        echo "1. Verify port 80 is accessible from the internet:"
        echo "   curl -v http://$DOMAIN/.well-known/acme-challenge/test"
        echo ""
        echo "2. Check DNS resolution:"
        echo "   nslookup $DOMAIN"
        echo ""
        echo "3. Check nginx configuration:"
        echo "   docker compose logs milal-frontend | grep -i acme"
        echo ""
        echo "4. Check certbot logs:"
        echo "   docker compose logs frontend-certbot-1"
        echo ""
        return 1
    fi
    
    # Verify real cert was obtained
    if [ ! -f "./certs/live/$DOMAIN/fullchain.pem" ]; then
        echo -e "${RED}ERROR: Certificate request failed for $DOMAIN${NC}"
        echo ""
        echo "The temporary self-signed certificate is still in place."
        echo "This is NOT suitable for production!"
        echo ""
        return 1
    fi
    
    # Verify cert is not self-signed
    if docker run --rm -v "$(pwd)/certs:/certs" alpine/openssl x509 -in /certs/live/$DOMAIN/fullchain.pem -noout -issuer 2>/dev/null | grep -q "CN=localhost"; then
        echo -e "${RED}ERROR: Certificate is still self-signed (CN=localhost)${NC}"
        echo "Let's Encrypt certificate was not successfully obtained."
        return 1
    fi
    
    echo ">> Reloading nginx with real certificate..."
    if [ "$SERVICE" = "Frontend" ]; then
        docker compose exec milal-frontend nginx -s reload 2>/dev/null || true
    else
        docker compose exec milal-nginx nginx -s reload 2>/dev/null || true
    fi
    
    echo -e "${GREEN}✓ SSL certificate installed for $DOMAIN${NC}"
    echo "  Location: ./certs/live/$DOMAIN/"
    echo "  - fullchain.pem"
    echo "  - privkey.pem"
}

# Function to sync certificates between frontend and backend
sync_certificates() {
    echo ""
    echo "=========================================="
    echo "Syncing certificates between frontend and backend"
    echo "=========================================="
    echo ""
    
    local DOMAIN="milalchurch.ca"
    
    # Create backend certs directory
    mkdir -p "backend/certs/live/$DOMAIN"
    
    # Copy from frontend to backend
    if [ -f "frontend/certs/live/$DOMAIN/fullchain.pem" ]; then
        cp "frontend/certs/live/$DOMAIN/fullchain.pem" "backend/certs/live/$DOMAIN/fullchain.pem"
        echo "✓ Copied fullchain.pem to backend"
    else
        echo "⚠ Frontend fullchain.pem not found, skipping"
    fi
    
    if [ -f "frontend/certs/live/$DOMAIN/privkey.pem" ]; then
        cp "frontend/certs/live/$DOMAIN/privkey.pem" "backend/certs/live/$DOMAIN/privkey.pem"
        chmod 600 "backend/certs/live/$DOMAIN/privkey.pem"
        echo "✓ Copied privkey.pem to backend"
    else
        echo "⚠ Frontend privkey.pem not found, skipping"
    fi
    
    echo ""
    echo -e "${GREEN}✓ Certificate sync complete${NC}"
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
        sync_certificates
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
echo "  • Frontend: $FRONTEND_DOMAIN (including *.${FRONTEND_DOMAIN})"
echo "  • Backend:  $BACKEND_DOMAIN (including *.${BACKEND_DOMAIN})"
echo ""
echo "IMPORTANT - Let's Encrypt Rate Limits:"
echo "  • Max 5 new certs per exact domain set per 7 days"
echo "  • If you hit the limit, use --force-renewal sparingly"
echo "  • Set STAGING=1 in this script for testing to avoid limits"
echo ""
echo "HTTPS Not Working? Troubleshooting:"
echo "  1. Verify port 80 is open: curl -v http://$FRONTEND_DOMAIN"
echo "  2. Check Azure NSG rules allow port 80 and 443"
echo "  3. Verify DNS resolution: nslookup $FRONTEND_DOMAIN"
echo "  4. View nginx errors: cd frontend && docker compose logs"
echo "  5. View certbot errors: cd frontend && docker compose logs frontend-certbot-1"
echo ""
echo "If certificate is still self-signed after running this script:"
echo "  • Port 80 may not be accessible from the internet"
echo "  • Check Azure Network Security Group (NSG) inbound rules"
echo "  • Ensure public IP is associated with DNS name"
echo "  • Try again after fixing network configuration"
echo ""
echo "Next step: Start all services"
echo "  cd frontend && docker compose up -d"
echo "  cd backend && docker compose up -d"
echo ""
echo "To renew certificates manually:"
echo "  cd frontend && docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  Then sync certificates: ./sync-ssl-certs.sh"
echo ""
echo "IMPORTANT: Certificate Sync"
echo "  Since frontend and backend share the same domain, keep certificates in sync:"
echo "  After any cert update, run: ./sync-ssl-certs.sh"
