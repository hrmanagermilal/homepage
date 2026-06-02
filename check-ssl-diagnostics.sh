#!/bin/bash
# Quick SSL diagnostics script

DOMAIN="milalchurch.ca"

echo "=========================================="
echo "SSL/TLS Diagnostics for $DOMAIN"
echo "=========================================="
echo ""

# Check DNS resolution
echo "1. Checking DNS resolution..."
if nslookup $DOMAIN 2>/dev/null | grep -q "Address:"; then
  echo "   ✓ DNS resolves to: $(nslookup $DOMAIN 2>/dev/null | grep "Address:" | tail -1)"
else
  echo "   ✗ DNS resolution failed"
fi
echo ""

# Check port 80 accessibility
echo "2. Checking port 80 accessibility..."
if curl -I --connect-timeout 5 http://$DOMAIN 2>/dev/null | head -1; then
  echo "   ✓ Port 80 is accessible"
else
  echo "   ✗ Port 80 is NOT accessible (required for ACME challenges)"
fi
echo ""

# Check certificate
echo "3. Checking installed certificate..."
if [ -f "frontend/certs/live/$DOMAIN/fullchain.pem" ]; then
  ISSUER=$(openssl x509 -in frontend/certs/live/$DOMAIN/fullchain.pem -noout -issuer 2>/dev/null)
  if echo "$ISSUER" | grep -q "CN=localhost"; then
    echo "   ✗ Certificate is SELF-SIGNED (temporary)"
  elif echo "$ISSUER" | grep -q "Let"; then
    echo "   ✓ Certificate is from Let's Encrypt"
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
echo "  • If certificate is self-signed, run: sudo ./init-ssl.sh all"
