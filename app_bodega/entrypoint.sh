#!/bin/bash

# Ajustar los permisos de las carpetas necesarias
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/app /var/www/config /var/www/database
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/app /var/www/config /var/www/database

# Ejecutar los comandos pasados al contenedor
exec "$@"
