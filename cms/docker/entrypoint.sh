#!/bin/sh
# Seed static uploads from the image into the mounted volume.
# /var/www/static-uploads/ is outside the bind-mounted /var/www/html/ so it
# is always the baked image content, never hidden by the live source mount.
# Files that already exist in the volume are never overwritten.
if [ -d /var/www/static-uploads ]; then
    cp -rn /var/www/static-uploads/. /var/www/html/public/uploads/
    chown -R www-data:www-data /var/www/html/public/uploads
fi

exec apache2-foreground
