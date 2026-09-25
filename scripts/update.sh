#!/bin/sh
set -eu

echo "Creando copia previa a la actualizacion..."
./scripts/backup.sh
docker compose build --pull moodle cron
docker compose up -d
docker compose exec -T moodle php admin/cli/upgrade.php --non-interactive
docker compose exec -T moodle php admin/cli/purge_caches.php
echo "Actualizacion terminada. Ejecuta ./scripts/smoke-test.sh."

