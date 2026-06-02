#!/bin/bash
# Sync SSL certificates between frontend and backend
# Run this after obtaining new certificates from Let's Encrypt

FRONTEND_CERTS="./frontend/certs"
BACKEND_CERTS="./backend/certs"
DOMAIN="milalchurch.ca"

echo "Syncing SSL certificates..."
echo ""

# Create backend certs directory if it doesn't exist
mkdir -p "$BACKEND_CERTS/live/$DOMAIN"

# Copy certificates
if [ -f "$FRONTEND_CERTS/live/$DOMAIN/fullchain.pem" ]; then
    cp "$FRONTEND_CERTS/live/$DOMAIN/fullchain.pem" "$BACKEND_CERTS/live/$DOMAIN/fullchain.pem"
    echo "✓ Copied fullchain.pem"
else
    echo "✗ Frontend fullchain.pem not found"
    exit 1
fi

if [ -f "$FRONTEND_CERTS/live/$DOMAIN/privkey.pem" ]; then
    cp "$FRONTEND_CERTS/live/$DOMAIN/privkey.pem" "$BACKEND_CERTS/live/$DOMAIN/privkey.pem"
    echo "✓ Copied privkey.pem"
else
    echo "✗ Frontend privkey.pem not found"
    exit 1
fi

# Set proper permissions
chmod 644 "$BACKEND_CERTS/live/$DOMAIN/fullchain.pem"
chmod 600 "$BACKEND_CERTS/live/$DOMAIN/privkey.pem"

echo ""
echo "Certificate sync complete!"
echo "Certificates synchronized to:"
echo "  - $BACKEND_CERTS/live/$DOMAIN/"
echo ""
echo "Next: Restart backend services"
echo "  cd backend && docker compose down && docker compose up -d"
