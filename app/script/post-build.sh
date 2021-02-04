#!/bin/bash
if [ ! -d "/var/www/html/vendor" ]; then
    composer install
fi

if [ ! -f "/var/www/html/.env" ]; then
    # Create a environment file and update the variables for your structure
    cp .env.example .env
fi

php -S 0.0.0.0:80 -t public/ #  for development