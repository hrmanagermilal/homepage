# SSL Certificate Issue - Self-Signed Certificate Error

## Problem
Frontend is serving a temporary self-signed certificate (CN=localhost) instead of the Let's Encrypt certificate.

## Root Cause
The `init-ssl.sh` script failed to obtain a Let's Encrypt certificate due to:
```
Client with the currently selected authenticator does not support any combination of challenges
```

This typically means **port 80 is not accessible from the internet**, which is required for ACME domain verification.

## Solution

### Step 1: Verify Port 80 is Accessible
From WSL/Linux terminal in the workspace:
```bash
# Check if port 80 responds
curl -I http://milalchurch.ca

# Check if DNS resolves
nslookup milalchurch.ca

# Run diagnostics script
./check-ssl-diagnostics.sh
```

### Step 2: Check Azure Network Security Group (NSG)
In Azure Portal:
1. Go to the VM or NIC associated with public IP `20.120.81.234`
2. Check Network Security Group > Inbound Rules
3. **Ensure port 80 (HTTP) is allowed:**
   - Protocol: TCP
   - Source Port Range: *
   - Destination Port: 80
   - Action: Allow
4. If not present, add a new inbound rule allowing HTTP on port 80

### Step 3: Retry SSL Certificate Setup
```bash
cd /mnt/c/workspace-milal/homepage
sudo ./init-ssl.sh all
```

## Troubleshooting

### If port 80 shows "blocked" in diagnostics:
1. Open Azure Portal
2. Find the Network Security Group (NSG) for your VM
3. Add/Enable inbound rule for port 80
4. Wait a few minutes for the rule to propagate
5. Retry `./init-ssl.sh all`

### If DNS doesn't resolve:
1. Check your domain registrar's DNS settings
2. Ensure A record points to `20.120.81.234`
3. Wait for DNS propagation (up to 24 hours)
4. Verify with: `nslookup milalchurch.ca`

### Manual Verification
After certificates are obtained, verify they're from Let's Encrypt:
```bash
# Check certificate issuer
openssl x509 -in frontend/certs/live/milalchurch.ca/fullchain.pem -noout -issuer
# Should show: issuer=C = US, O = Let's Encrypt, CN = R3
# NOT: issuer=CN=localhost
```

## Certificate Details
- **Current**: Self-signed (CN=localhost), valid 1 day
- **Target**: Let's Encrypt wildcard cert for *.milalchurch.ca
- **Renewal**: Automatic (handled by certbot)

## Emergency Workaround (Not Recommended)
If you cannot open port 80:
1. Use DNS-01 challenge (requires DNS API credentials)
2. Use email-based verification
3. Temporarily open port 80, get cert, then close port (less secure)
