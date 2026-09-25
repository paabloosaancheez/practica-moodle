#!/bin/sh
set -eu

base_url="${MOODLE_URL:-http://localhost:8080}"
status="$(curl -sS -o /tmp/moodle-home.html -w '%{http_code}' "${base_url}/login/index.php")"

if [ "$status" != "200" ]; then
  echo "ERROR: Moodle devolvio HTTP ${status}"
  exit 1
fi

docker compose exec -T database mariadb-admin ping \
  -u"${DB_USER}" -p"${DB_PASSWORD}" >/dev/null

docker compose exec -T moodle php -r '
define("CLI_SCRIPT", true);
require "/var/www/html/config.php";
$course = $DB->get_record("course", ["shortname" => "FOTO101"], "*", MUST_EXIST);
$activities = get_fast_modinfo($course)->get_cms();
if (count($activities) < 4) { fwrite(STDERR, "Faltan actividades\n"); exit(1); }
echo "Curso: {$course->fullname}; actividades: " . count($activities) . "\n";
'

echo "OK: web, base de datos, curso y actividades funcionan."

