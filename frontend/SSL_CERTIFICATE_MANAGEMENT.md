# Frontend SSL Certificate Management

This directory contains scripts and configuration for managing SSL certificates for the **milalchurch.ca** domain using Let's Encrypt and Certbot.

## Quick Start

### Initial Setup (First Time)

Run this once to obtain the initial SSL certificate:

```bash
cd frontend
./init-ssl.sh
```

This script:
1. Creates a temporary self-signed certificate so nginx can start
2. Starts the frontend container
3. Uses certbot to request a real Let's Encrypt certificate
4. Reloads nginx with the real certificate

### Manual Renewal

To manually renew certificates before expiration:

```bash
cd frontend
docker compose run --rm certbot renew --webroot -w /var/www/certbot
docker compose exec frontend nginx -s reload
```

## Directory Structure

```
frontend/
├── certs/                 # SSL certificates (mounted by nginx)
│   ├── fullchain.pem     # Full certificate chain
│   ├── privkey.pem       # Private key
│   ├── chain.pem         # Intermediate certificates (auto-generated)
│   └── cert.pem          # Certificate only (auto-generated)
├── certbot/
│   └── www/              # Certbot webroot for ACME challenges
├── docker-compose.yml    # Service definitions
├── nginx.conf            # Nginx config with ACME support
└── init-ssl.sh           # Initial certificate setup script
```

## Configuration

### Domain
- **Frontend Domain**: milalchurch.ca
- **Email**: dev@milalchurch.com (change in `init-ssl.sh` if needed)

### Certbot Usage
- Certificates are obtained and renewed on-demand using `docker compose run --rm certbot`
- No persistent certbot service; uses same approach as backend
- Webroot authentication method (ACME challenges served via HTTP)

## Renewal

### Automatic Renewal (Recommended)

Set up a cron job on the host server to check for renewal every 12 hours:

```bash
# On the host machine (not in container), add to crontab:
0 */12 * * * cd /path/to/frontend && docker compose run --rm certbot renew --webroot -w /var/www/certbot && docker compose exec frontend nginx -s reload
```

### Manual Renewal

If you need to renew manually before expiration:

```bash
cd frontend
docker compose run --rm certbot renew --webroot -w /var/www/certbot
docker compose exec frontend nginx -s reload
```

### Force Renewal (for testing)

```bash
cd frontend
docker compose run --rm certbot certonly \
  --webroot \
  -w /var/www/certbot \
  -d milalchurch.ca \
  --email dev@milalchurch.com \
  --agree-tos \
  --force-renewal
docker compose exec frontend nginx -s reload
```

## Troubleshooting

### Check Certificate Status
```bash
cd frontend
docker compose run --rm certbot certificates
```

### View Certificate Details
```bash
cd frontend
docker compose run --rm certbot openssl x509 \
  -in /etc/letsencrypt/live/milalchurch.ca/cert.pem \
  -text -noout
```

### Check Certbot Logs
```bash
cd frontend
docker compose run --rm certbot certbot --help
```

### Test with Staging Certificate (no rate limits)
To test the setup without hitting Let's Encrypt rate limits:

1. Edit `init-ssl.sh` and set `STAGING=1`
2. Run `./init-ssl.sh`
3. Verify it works, then set `STAGING=0` and re-run with `--force-renewal`

## Common Issues

### Issue: "Couldn't find a public suffix that matches"
This usually means DNS is not set up correctly. Verify that `milalchurch.ca` resolves to your server's IP.

```bash
nslookup milalchurch.ca
# or
dig milalchurch.ca
```

### Issue: "Connection refused" from ACME server
Check that:
1. Nginx is running and port 80 is accessible
2. The `.well-known/acme-challenge/` location is properly configured in nginx.conf
3. There are no firewall rules blocking port 80
4. Permissions on `./certbot/www` directory are correct

### Issue: Certificate renewal fails
1. Check certbot output for detailed error messages
2. Verify the domain DNS is still pointing to your server
3. Ensure port 80 is accessible and not blocked
4. Try with verbose logging:
   ```bash
   docker compose run --rm certbot certonly \
     --webroot \
     -w /var/www/certbot \
     -d milalchurch.ca \
     --email dev@milalchurch.com \
     --agree-tos \
     -v
   ```

### Issue: "Permission denied" errors
Ensure the `./certbot/www` and `./certs` directories are writable:

```bash
chmod 755 certbot/www
chmod 755 certs
```

## SSL/TLS Configuration

The nginx configuration uses:
- **Protocols**: TLSv1.2 and TLSv1.3
- **Ciphers**: Modern, recommended ciphers for security
- **Session Cache**: Shared SSL cache (10MB) with 1-day timeout
- **HTTP/2**: Enabled for better performance

## Security Notes

1. **Private Key Protection**: `privkey.pem` is mounted as read-only to the nginx container
2. **Certificate Renewal**: Manage via cron job on host or manual command execution
3. **ACME Challenge**: `.well-known/acme-challenge/` is accessible on HTTP (required for validation)
4. **Staging Certificates**: For testing, set `STAGING=1` in `init-ssl.sh` to use Let's Encrypt staging

## Monitoring

Monitor certificate expiration via:

```bash
# Check remaining days
docker compose run --rm certbot certificates | grep -A 5 "milalchurch.ca"

# Check certificate expiration date
docker run --rm -v "$(pwd)/certs:/certs:ro" alpine/openssl \
  x509 -in /certs/fullchain.pem -noout -dates
```

## References

- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Certbot Documentation](https://certbot.eff.org/docs/)
- [Nginx SSL Configuration](https://nginx.org/en/docs/http/ngx_http_ssl_module.html)
- [ACME Challenge Types](https://letsencrypt.org/docs/challenge-types/)
