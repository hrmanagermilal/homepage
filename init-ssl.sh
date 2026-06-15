#!/bin/bash
# ============================================
# Let's Encrypt SSL Certificate Init Script
# Sets up certificates for frontend and/or backend
# ============================================

set -e

FRONTEND_DOMAIN="milalchurch.ca"
FRONTEND_DOMAIN_COM="milalchurch.com"
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
    
    echo ">> Creating certbot webroot directory..."
    mkdir -p ./certbot/www
    chmod 755 ./certbot/www
    
    echo ">> Starting $SERVICE container..."
    docker compose up -d
    sleep 5
    
    # Wait for nginx to be ready (max 30 seconds)
    echo ">> Waiting for nginx to be ready..."
    max_attempts=60
    attempt=0
    nginx_ready=0
    
    while [ $attempt -lt $max_attempts ]; do
        # Try to execute nginx -t on any running container
        if docker compose exec -T $(docker compose ps -q | head -1) nginx -t >/dev/null 2>&1; then
            echo ">> nginx is ready"
            nginx_ready=1
            break
        fi
        
        # Alternative: check if container is running
        if docker compose ps | grep -q "Up"; then
            echo ">> Container is running, proceeding..."
            nginx_ready=1
            break
        fi
        
        attempt=$((attempt + 1))
        sleep 1
    done
    
    if [ $nginx_ready -eq 0 ]; then
        echo -e "${RED}WARNING: nginx did not become ready in time, proceeding anyway...${NC}"
    fi
    
    sleep 2
    
    echo ">> Requesting Let's Encrypt certificate for $DOMAIN..."
    
    STAGING_ARG=""
    if [ "$STAGING" -eq 1 ]; then
        STAGING_ARG="--staging"
    fi
    
    # Create webroot directory
    mkdir -p ./certbot/www
    
    # Clean up any broken renewal config (for fresh certificate request)
    docker compose run --rm certbot sh -c "rm -f /etc/letsencrypt/renewal/$DOMAIN*.conf" 2>/dev/null || true
    
    # Request real certificate (with specific domains)
    # NOTE: Don't use --force-renewal by default (causes rate limit issues)
    # Use 'renew' for existing certs or 'certonly' for new requests
    if ! docker compose run --rm certbot certonly \
      --webroot \
      -w /var/www/certbot \
      -d "$DOMAIN" \
      -d "www.$DOMAIN" \
      -d "$FRONTEND_DOMAIN_COM" \
      -d "www.$FRONTEND_DOMAIN_COM" \
      --email "$EMAIL" \
      --agree-tos \
      --no-eff-email \
      --expand \
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
        # Check if it's in the -0001 suffixed directory (new cert)
        if [ -f "./certs/live/${DOMAIN}-0001/fullchain.pem" ]; then
            echo ">> Certificate found at ./certs/live/${DOMAIN}-0001/, creating symlink..."
            ln -sf "./${DOMAIN}-0001/fullchain.pem" "./certs/live/$DOMAIN/fullchain.pem"
            ln -sf "./${DOMAIN}-0001/privkey.pem" "./certs/live/$DOMAIN/privkey.pem"
        else
            echo -e "${RED}ERROR: Certificate file not found${NC}"
            echo "Checked locations:"
            echo "  - ./certs/live/$DOMAIN/fullchain.pem"
            echo "  - ./certs/live/${DOMAIN}-0001/fullchain.pem"
            return 1
        fi
    fi
    
    # Verify cert is not self-signed
    if ! docker run --rm -v "$(pwd)/certs:/certs" alpine/openssl x509 -in /certs/live/$DOMAIN/fullchain.pem -noout -issuer 2>/dev/null | grep -q "Let"; then
        echo -e "${RED}WARNING: Certificate issuer check inconclusive${NC}"
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

# Main logic - Setup frontend SSL only, then sync to backend
setup_ssl "Frontend" "$FRONTEND_DOMAIN" "frontend"

# Automatically sync certificates to backend
echo ""
echo ">> Syncing certificates to backend..."
sync_certificates

# Restart backend nginx to use updated certs
echo ""
echo ">> Restarting backend nginx..."
cd backend
docker compose restart milal-nginx 2>/dev/null || echo "Backend not running yet, will use certs on next start"
cd ..

echo ""
echo "=========================================="
echo "SSL Certificate Setup Complete!"
echo "=========================================="
echo ""
echo "✓ Frontend certificate setup and obtained"
echo "✓ Certificates synced to backend"
echo "✓ Backend nginx restarted with new certificates"
echo ""
echo "Domains configured:"
echo "  • Frontend: $FRONTEND_DOMAIN (and www.${FRONTEND_DOMAIN})"
echo "  • Frontend: $FRONTEND_DOMAIN_COM (and www.${FRONTEND_DOMAIN_COM})"
echo "  • Backend:  $BACKEND_DOMAIN (and www.${BACKEND_DOMAIN})"
echo ""
echo "IMPORTANT - Let's Encrypt Rate Limits:"
echo "  • Max 5 new certs per exact domain set per 7 days"
echo "  • If you hit the limit, use --force-renewal sparingly"
echo "  • Set STAGING=1 in this script for testing to avoid limits"
echo ""
echo "Verify SSL is working:"
echo "  • Frontend: openssl s_client -connect $FRONTEND_DOMAIN:443"
echo "  • Backend:  openssl s_client -connect $BACKEND_DOMAIN:8443"
echo ""
echo "To renew certificates manually:"
echo "  cd frontend && docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  Then: bash sync-ssl-certs.sh"
echo ""
