#!/bin/bash
# ============================================
# SSL Certificate Verification Script
# Checks if SSL certificates are working correctly
# ============================================

set -e

DOMAIN="milalchurch.ca"
CERT_PATH="./certs/live/$DOMAIN/fullchain.pem"
KEY_PATH="./certs/live/$DOMAIN/privkey.pem"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "SSL Certificate Verification Report"
echo "=========================================="
echo ""

# Check 1: Certificate files exist
echo "[CHECK 1] Certificate Files"
if [ -f "$CERT_PATH" ]; then
    echo -e "${GREEN}✓${NC} Certificate file exists: $CERT_PATH"
else
    echo -e "${RED}✗${NC} Certificate file missing: $CERT_PATH"
    exit 1
fi

if [ -f "$KEY_PATH" ]; then
    echo -e "${GREEN}✓${NC} Private key exists: $KEY_PATH"
else
    echo -e "${RED}✗${NC} Private key missing: $KEY_PATH"
    exit 1
fi
echo ""

# Check 2: Certificate validity
echo "[CHECK 2] Certificate Validity"
CERT_INFO=$(docker run --rm -v "$(pwd)/certs:/certs:ro" alpine/openssl x509 -in /certs/live/$DOMAIN/fullchain.pem -text -noout 2>/dev/null || echo "")

if [ -z "$CERT_INFO" ]; then
    echo -e "${YELLOW}⚠${NC} Could not read certificate details (Docker may not be running)"
else
    # Extract dates
    NOT_BEFORE=$(echo "$CERT_INFO" | grep "Not Before:" | cut -d: -f2-)
    NOT_AFTER=$(echo "$CERT_INFO" | grep "Not After:" | cut -d: -f2-)
    SUBJECT=$(echo "$CERT_INFO" | grep "Subject:" | cut -d= -f2- | head -1)
    
    echo "   Subject: $SUBJECT"
    echo "   Valid From: $NOT_BEFORE"
    echo "   Valid Until: $NOT_AFTER"
    
    # Check if certificate is for correct domain
    SUBJECT_CN=$(echo "$CERT_INFO" | grep "Subject:" | grep -oP 'CN\s*=\s*\K[^,]*' || echo "")
    if [[ "$SUBJECT_CN" == *"$DOMAIN"* ]] || [[ "$SUBJECT_CN" == "localhost" ]]; then
        echo -e "${GREEN}✓${NC} Certificate appears to be for correct domain"
    else
        echo -e "${YELLOW}⚠${NC} Certificate subject CN: $SUBJECT_CN (expected: $DOMAIN or localhost)"
    fi
fi
echo ""

# Check 3: Docker services
echo "[CHECK 3] Docker Services"
if command -v docker >/dev/null 2>&1; then
    if docker ps --filter "name=milal-frontend" --quiet | grep -q .; then
        echo -e "${GREEN}✓${NC} Frontend container is running"
    else
        echo -e "${YELLOW}⚠${NC} Frontend container is not running (start with: docker compose up -d)"
    fi
else
    echo -e "${YELLOW}⚠${NC} Docker is not installed or not in PATH"
fi
echo ""

# Check 4: Nginx configuration
echo "[CHECK 4] Nginx Configuration"
if grep -q "ssl_certificate /etc/nginx/certs/live/$DOMAIN/fullchain.pem" nginx.conf; then
    echo -e "${GREEN}✓${NC} Nginx is configured to use SSL certificate"
else
    echo -e "${RED}✗${NC} Nginx SSL certificate path not found in nginx.conf"
fi

if grep -q "\.well-known/acme-challenge" nginx.conf; then
    echo -e "${GREEN}✓${NC} Nginx is configured for ACME challenges"
else
    echo -e "${YELLOW}⚠${NC} ACME challenge location not found in nginx.conf"
fi
echo ""

# Check 5: Directory structure
echo "[CHECK 5] Directory Structure"
if [ -d "certbot/www" ]; then
    echo -e "${GREEN}✓${NC} Certbot webroot directory exists"
else
    echo -e "${YELLOW}⚠${NC} Certbot webroot directory missing (will be created on first run)"
fi

if [ -d "certs" ]; then
    echo -e "${GREEN}✓${NC} Certificates directory exists"
else
    echo -e "${YELLOW}⚠${NC} Certificates directory missing"
fi
echo ""

# Check 6: docker-compose.yml
echo "[CHECK 6] Docker Compose Configuration"
if grep -q "certbot:" docker-compose.yml; then
    echo -e "${GREEN}✓${NC} Certbot service is defined in docker-compose.yml"
else
    echo -e "${RED}✗${NC} Certbot service not found in docker-compose.yml"
fi

if grep -q "certbot/www" docker-compose.yml; then
    echo -e "${GREEN}✓${NC} Certbot webroot volume is configured"
else
    echo -e "${RED}✗${NC} Certbot webroot volume not configured in docker-compose.yml"
fi
echo ""

# Check 7: Port accessibility (if running)
if command -v docker >/dev/null 2>&1 && docker ps --filter "name=milal-frontend" --quiet | grep -q .; then
    echo "[CHECK 7] Port Accessibility"
    
    # Try port 80
    timeout 2 bash -c "echo > /dev/tcp/127.0.0.1/80" 2>/dev/null && \
        echo -e "${GREEN}✓${NC} Port 80 (HTTP) is accessible" || \
        echo -e "${YELLOW}⚠${NC} Port 80 (HTTP) not accessible (may be blocked by firewall)"
    
    # Try port 443
    timeout 2 bash -c "echo > /dev/tcp/127.0.0.1/443" 2>/dev/null && \
        echo -e "${GREEN}✓${NC} Port 443 (HTTPS) is accessible" || \
        echo -e "${YELLOW}⚠${NC} Port 443 (HTTPS) not accessible (may be blocked by firewall)"
    echo ""
fi

echo "=========================================="
echo "Verification Complete!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. If this is first deployment, run: ./init-ssl.sh"
echo "2. Start services: docker compose up -d"
echo "3. Test HTTPS: curl -I https://localhost (on host with port forwarding)"
echo ""
echo "To renew certificate manually:"
echo "  docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  docker compose exec frontend nginx -s reload"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "SSL Certificate Verification Report"
echo "=========================================="
echo ""

# Check 1: Certificate files exist
echo "[CHECK 1] Certificate Files"
if [ -f "$CERT_PATH" ]; then
    echo -e "${GREEN}✓${NC} Certificate file exists: $CERT_PATH"
else
    echo -e "${RED}✗${NC} Certificate file missing: $CERT_PATH"
    exit 1
fi

if [ -f "$KEY_PATH" ]; then
    echo -e "${GREEN}✓${NC} Private key exists: $KEY_PATH"
else
    echo -e "${RED}✗${NC} Private key missing: $KEY_PATH"
    exit 1
fi
echo ""

# Check 2: Certificate validity
echo "[CHECK 2] Certificate Validity"
CERT_INFO=$(docker run --rm -v "$(pwd)/certs:/certs:ro" alpine/openssl x509 -in /certs/fullchain.pem -text -noout 2>/dev/null || echo "")

if [ -z "$CERT_INFO" ]; then
    echo -e "${YELLOW}⚠${NC} Could not read certificate details (Docker may not be running)"
else
    # Extract dates
    NOT_BEFORE=$(echo "$CERT_INFO" | grep "Not Before:" | cut -d: -f2-)
    NOT_AFTER=$(echo "$CERT_INFO" | grep "Not After:" | cut -d: -f2-)
    SUBJECT=$(echo "$CERT_INFO" | grep "Subject:" | cut -d= -f2- | head -1)
    
    echo "   Subject: $SUBJECT"
    echo "   Valid From: $NOT_BEFORE"
    echo "   Valid Until: $NOT_AFTER"
    
    # Check if certificate is for correct domain
    SUBJECT_CN=$(echo "$CERT_INFO" | grep "Subject:" | grep -oP 'CN\s*=\s*\K[^,]*' || echo "")
    if [[ "$SUBJECT_CN" == *"$DOMAIN"* ]] || [[ "$SUBJECT_CN" == "localhost" ]]; then
        echo -e "${GREEN}✓${NC} Certificate appears to be for correct domain"
    else
        echo -e "${YELLOW}⚠${NC} Certificate subject CN: $SUBJECT_CN (expected: $DOMAIN or localhost)"
    fi
fi
echo ""

# Check 3: Docker services
echo "[CHECK 3] Docker Services"
if command -v docker >/dev/null 2>&1; then
    if docker ps --filter "name=milal-frontend" --quiet | grep -q .; then
        echo -e "${GREEN}✓${NC} Frontend container is running"
    else
        echo -e "${YELLOW}⚠${NC} Frontend container is not running (start with: docker compose up -d)"
    fi
else
    echo -e "${YELLOW}⚠${NC} Docker is not installed or not in PATH"
fi
echo ""

# Check 4: Nginx configuration
echo "[CHECK 4] Nginx Configuration"
if grep -q "ssl_certificate /etc/nginx/certs/fullchain.pem" nginx.conf; then
    echo -e "${GREEN}✓${NC} Nginx is configured to use SSL certificate"
else
    echo -e "${RED}✗${NC} Nginx SSL certificate path not found in nginx.conf"
fi

if grep -q "\.well-known/acme-challenge" nginx.conf; then
    echo -e "${GREEN}✓${NC} Nginx is configured for ACME challenges"
else
    echo -e "${YELLOW}⚠${NC} ACME challenge location not found in nginx.conf"
fi
echo ""

# Check 5: Directory structure
echo "[CHECK 5] Directory Structure"
if [ -d "certbot/www" ]; then
    echo -e "${GREEN}✓${NC} Certbot webroot directory exists"
else
    echo -e "${YELLOW}⚠${NC} Certbot webroot directory missing (will be created on first run)"
fi

if [ -d "certs" ]; then
    echo -e "${GREEN}✓${NC} Certificates directory exists"
else
    echo -e "${YELLOW}⚠${NC} Certificates directory missing"
fi
echo ""

# Check 6: docker-compose.yml
echo "[CHECK 6] Docker Compose Configuration"
if grep -q "certbot/www" docker-compose.yml; then
    echo -e "${GREEN}✓${NC} Certbot webroot volume is configured"
else
    echo -e "${RED}✗${NC} Certbot webroot volume not configured in docker-compose.yml"
fi
echo ""

# Check 7: Port accessibility (if running)
if command -v docker >/dev/null 2>&1 && docker ps --filter "name=milal-frontend" --quiet | grep -q .; then
    echo "[CHECK 7] Port Accessibility"
    
    # Try port 80
    timeout 2 bash -c "echo > /dev/tcp/127.0.0.1/80" 2>/dev/null && \
        echo -e "${GREEN}✓${NC} Port 80 (HTTP) is accessible" || \
        echo -e "${YELLOW}⚠${NC} Port 80 (HTTP) not accessible (may be blocked by firewall)"
    
    # Try port 443
    timeout 2 bash -c "echo > /dev/tcp/127.0.0.1/443" 2>/dev/null && \
        echo -e "${GREEN}✓${NC} Port 443 (HTTPS) is accessible" || \
        echo -e "${YELLOW}⚠${NC} Port 443 (HTTPS) not accessible (may be blocked by firewall)"
    echo ""
fi

echo "=========================================="
echo "Verification Complete!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. If this is first deployment, run: ./init-ssl.sh"
echo "2. Start services: docker compose up -d"
echo "3. Test HTTPS: curl -I https://localhost (on host with port forwarding)"
echo ""
echo "To renew certificate manually:"
echo "  docker compose run --rm certbot renew --webroot -w /var/www/certbot"
echo "  docker compose exec frontend nginx -s reload"
