#!/bin/sh
set -eu

stamp="$(date +%Y%m%d-%H%M%S)"
mkdir -p backups

docker compose exec -T database mariadb-dump \
  -u"${DB_USER}" -p"${DB_PASSWORD}" "${DB_NAME}" \
  | gzip > "backups/moodle-db-${stamp}.sql.gz"

docker compose run --rm --no-deps --entrypoint tar moodle \
  -czf - -C /var moodledata > "backups/moodledata-${stamp}.tar.gz"

echo "Copias creadas en backups/ con marca ${stamp}."

