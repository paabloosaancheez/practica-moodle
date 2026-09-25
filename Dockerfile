FROM moodlehq/moodle-php-apache:8.3

ARG MOODLE_BRANCH=MOODLE_405_STABLE

USER root
RUN apt-get update \
    && apt-get install -y --no-install-recommends git curl ca-certificates default-mysql-client \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/www/html/* \
    && git clone --depth 1 --branch "${MOODLE_BRANCH}" https://github.com/moodle/moodle.git /var/www/html \
    && rm -rf /var/www/html/.git \
    && mkdir -p /var/moodledata /opt/practica \
    && chown -R www-data:www-data /var/www/html /var/moodledata

COPY --chown=www-data:www-data docker/config.php /var/www/html/config.php
COPY docker/entrypoint.sh /usr/local/bin/practica-entrypoint
RUN chmod 0755 /usr/local/bin/practica-entrypoint

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/practica-entrypoint"]
CMD ["apache2-foreground"]

