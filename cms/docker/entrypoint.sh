#!/bin/sh
# Seed static uploads from the image into the mounted volume.
# Files that already exist in the volume are never overwritten.
if [ -d /var/www/html/static-uploads ]; then
    cp -rn /var/www/html/static-uploads/. /var/www/html/public/uploads/
    chown -R www-data:www-data /var/www/html/public/uploads
fi

exec apache2-foreground
