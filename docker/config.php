<?php
unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('DB_HOST') ?: 'database';
$CFG->dbname    = getenv('DB_NAME') ?: 'moodle';
$CFG->dbuser    = getenv('DB_USER') ?: 'moodle';
$CFG->dbpass    = getenv('DB_PASSWORD') ?: '';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist' => false,
    'dbport' => '',
    'dbsocket' => '',
    'dbcollation' => 'utf8mb4_unicode_ci',
];

$CFG->wwwroot   = getenv('MOODLE_URL') ?: 'http://localhost:8080';
$CFG->dataroot  = '/var/moodledata';
$CFG->admin     = 'admin';
$CFG->directorypermissions = 02770;
$CFG->passwordsaltmain = hash('sha256', (getenv('DB_PASSWORD') ?: 'local') . '-practica-moodle');

// Medidas apropiadas para una instalación local de demostración.
$CFG->forcelogin = false;
$CFG->cookiesecure = str_starts_with($CFG->wwwroot, 'https://');
$CFG->cookiehttponly = true;
$CFG->cronclionly = true;
$CFG->disableupdatenotifications = false;

require_once(__DIR__ . '/lib/setup.php');

