#!/bin/bash
# Quick SSL diagnostics script

DOMAIN_CA="milalchurch.ca"
DOMAIN_COM="milalchurch.com"
CERT_DIR="frontend/certs/live/$DOMAIN_CA"

echo "=========================================="
echo "SSL/TLS Diagnostics for $DOMAIN_CA / $DOMAIN_COM"
echo "=========================================="
echo ""

# Check DNS resolution
echo "1. Checking DNS resolution..."
for DOMAIN in "$DOMAIN_CA" "$DOMAIN_COM"; do
  if nslookup $DOMAIN 2>/dev/null | grep -q "Address:"; then
    echo "   ✓ $DOMAIN resolves to: $(nslookup $DOMAIN 2>/dev/null | grep "Address:" | tail -1)"
  else
    echo "   ✗ DNS resolution failed for $DOMAIN"
  fi
done
echo ""

# Check port 80 accessibility
echo "2. Checking port 80 accessibility..."
for DOMAIN in "$DOMAIN_CA" "$DOMAIN_COM"; do
  if curl -I --connect-timeout 5 http://$DOMAIN 2>/dev/null | head -1; then
    echo "   ✓ Port 80 is accessible for $DOMAIN"
  else
    echo "   ✗ Port 80 is NOT accessible for $DOMAIN (required for ACME challenges)"
  fi
done
echo ""

# Check certificate (shared cert covers both domains)
echo "3. Checking installed certificate..."
if [ -f "$CERT_DIR/fullchain.pem" ]; then
  ISSUER=$(openssl x509 -in "$CERT_DIR/fullchain.pem" -noout -issuer 2>/dev/null)
  SANS=$(openssl x509 -in "$CERT_DIR/fullchain.pem" -noout -text 2>/dev/null | grep -A1 "Subject Alternative Name" | tail -1)
  if echo "$ISSUER" | grep -q "CN=localhost"; then
    echo "   ✗ Certificate is SELF-SIGNED (temporary)"
  elif echo "$ISSUER" | grep -q "Let"; then
    echo "   ✓ Certificate is from Let's Encrypt"
    echo "   SANs: $SANS"
    if echo "$SANS" | grep -q "$DOMAIN_COM"; then
      echo "   ✓ Certificate covers $DOMAIN_COM"
    else
      echo "   ✗ Certificate does NOT cover $DOMAIN_COM — re-run: sudo ./init-ssl.sh"
    fi
  else
    echo "   ? Unknown issuer: $ISSUER"
  fi
else
  echo "   ✗ No certificate installed"
fi
echo ""

# Check Docker containers
echo "4. Checking Docker containers..."
if docker ps | grep -q "milal-frontend"; then
  echo "   ✓ Frontend container is running"
else
  echo "   ✗ Frontend container is NOT running"
fi
echo ""

echo "Troubleshooting:"
echo "  • If port 80 is NOT accessible, check Azure NSG inbound rules"
echo "  • If DNS doesn't resolve, check Azure DNS or your registrar"
echo "  • If certificate is self-signed or missing $DOMAIN_COM, run: sudo ./init-ssl.sh"
