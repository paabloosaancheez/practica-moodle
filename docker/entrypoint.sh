#!/bin/sh
set -eu

cd /var/www/html
mkdir -p /var/moodledata
chown -R www-data:www-data /var/moodledata

until mysqladmin ping -h "${DB_HOST}" -u "${DB_USER}" -p"${DB_PASSWORD}" --silent; do
  echo "Esperando a MariaDB..."
  sleep 3
done

if [ ! -f /var/moodledata/.moodle-installed ]; then
  if ! mysql -h "${DB_HOST}" -u "${DB_USER}" -p"${DB_PASSWORD}" "${DB_NAME}" \
    -Nse "SHOW TABLES LIKE 'mdl_config'" | grep -q '^mdl_config$'; then
    echo "Instalando Moodle..."
    runuser -u www-data -- php admin/cli/install_database.php \
      --agree-license \
      --lang=es \
      --fullname="${MOODLE_SITE_NAME}" \
      --shortname="${MOODLE_SITE_SHORTNAME}" \
      --adminuser="${MOODLE_ADMIN_USER}" \
      --adminpass="${MOODLE_ADMIN_PASSWORD}" \
      --adminemail="${MOODLE_ADMIN_EMAIL}"
  else
    echo "La base de Moodle ya existe; se reanuda el aprovisionamiento."
  fi

  runuser -u www-data -- php /opt/practica/scripts/provision.php
  touch /var/moodledata/.moodle-installed
  chown www-data:www-data /var/moodledata/.moodle-installed
fi

runuser -u www-data -- php admin/cli/upgrade.php --non-interactive
exec "$@"
