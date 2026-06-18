#!/bin/bash
# Usage: ./set-youtube-api-key.sh <YOUR_API_KEY>
# Updates YOUTUBE_API_KEY in docker-compose.yml

set -e

if [ -z "$1" ]; then
  echo "Usage: $0 <YOUTUBE_API_KEY>"
  exit 1
fi

API_KEY="$1"
COMPOSE_FILE="$(dirname "$0")/docker-compose.yml"

if [ ! -f "$COMPOSE_FILE" ]; then
  echo "Error: docker-compose.yml not found at $COMPOSE_FILE"
  exit 1
fi

sed -i "s|YOUTUBE_API_KEY:.*|YOUTUBE_API_KEY: ${API_KEY}|" "$COMPOSE_FILE"

echo "YOUTUBE_API_KEY updated in $COMPOSE_FILE"
echo "Restart the container to apply: docker compose up -d --no-deps app"
