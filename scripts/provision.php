<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');
require_once($CFG->dirroot . '/course/modlib.php');

function add_activity(stdClass $course, string $modname, string $name, string $intro, array $extra = []): stdClass {
    global $DB;

    $existing = $DB->get_record($modname, ['course' => $course->id, 'name' => $name]);
    if ($existing) {
        return get_coursemodule_from_instance($modname, $existing->id, $course->id, false, MUST_EXIST);
    }

    $module = $DB->get_record('modules', ['name' => $modname], '*', MUST_EXIST);
    $data = (object) array_merge([
        'course' => $course->id,
        'module' => $module->id,
        'modulename' => $modname,
        'section' => 1,
        'visible' => 1,
        'name' => $name,
        'intro' => $intro,
        'introformat' => FORMAT_HTML,
        'completion' => COMPLETION_TRACKING_MANUAL,
        'groupmode' => NOGROUPS,
    ], $extra);

    return add_moduleinfo($data, $course);
}

$course = $DB->get_record('course', ['shortname' => 'FOTO101']);
if (!$course) {
    $category = core_course_category::get_default();
    $course = create_course((object) [
        'fullname' => 'Fotografia digital desde cero',
        'shortname' => 'FOTO101',
        'category' => $category->id,
        'summary' => '<p>Curso practico para dominar la exposicion, la composicion y la edicion basica.</p>',
        'summaryformat' => FORMAT_HTML,
        'format' => 'topics',
        'numsections' => 4,
        'visible' => 1,
        'enablecompletion' => 1,
    ]);
} else {
    mtrace('El curso FOTO101 ya existe; se completará sin duplicar contenido.');
}

$password = getenv('STUDENT_INITIAL_PASSWORD');
if (!$password) {
    throw new moodle_exception('Falta la variable STUDENT_INITIAL_PASSWORD');
}
// Esta instalacion es local y no dispone de un servidor SMTP/sendmail.
set_config('noreplyaddress', 'noreply@example.com');
set_config('noemailever', 1);

$csv = '/opt/practica/data/usuarios.csv';
$handle = fopen($csv, 'r');
if ($handle === false) {
    throw new moodle_exception('No se pudo abrir ' . $csv);
}
$header = fgetcsv($handle);
$manual = enrol_get_plugin('manual');
$instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual'], '*', MUST_EXIST);

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) !== count($header) || count(array_filter($row, static fn($value) => trim((string) $value) !== '')) === 0) {
        continue;
    }
    $item = array_combine($header, $row);
    $user = $DB->get_record('user', ['username' => $item['username'], 'mnethostid' => $CFG->mnet_localhost_id]);
    if (!$user) {
        $user = (object) [
            'auth' => 'manual',
            'confirmed' => 1,
            'mnethostid' => $CFG->mnet_localhost_id,
            'username' => $item['username'],
            'password' => hash_internal_user_password($password),
            'firstname' => $item['firstname'],
            'lastname' => $item['lastname'],
            'email' => $item['email'],
            'forcepasswordchange' => 1,
        ];
        $user->id = user_create_user($user, false, false);
    }
    $role = $DB->get_record('role', ['shortname' => $item['role']], '*', MUST_EXIST);
    if (!$DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $user->id])) {
        $manual->enrol_user($instance, $user->id, $role->id);
    }
}
fclose($handle);

add_activity(
    $course,
    'page',
    'Lectura y video: el triangulo de exposicion',
    '<h3>Objetivo</h3><p>Comprender como se relacionan apertura, velocidad e ISO.</p>' .
    '<p>Lee el resumen, visualiza el video indicado por el profesor y marca la actividad como completada.</p>',
    [
        'content' => '<h2>El triangulo de exposicion</h2><p>La apertura controla la luz y la profundidad de campo; la velocidad controla el movimiento; el ISO amplifica la señal y puede añadir ruido.</p><p><strong>Tarea:</strong> anota un ejemplo de ajuste para congelar movimiento y otro para obtener fondo desenfocado.</p>',
        'contentformat' => FORMAT_HTML,
        'display' => 5,
        'printintro' => 1,
        'printlastmodified' => 1,
    ]
);

add_activity(
    $course,
    'assign',
    'Actividad evaluable: foto con regla de los tercios',
    '<p>Entrega una fotografia propia en JPG aplicando la regla de los tercios y añade una breve justificacion. Calificacion maxima: 10 puntos.</p>',
    [
        'grade' => 10,
        'duedate' => time() + 14 * DAYSECS,
        'cutoffdate' => time() + 21 * DAYSECS,
        'submissiondrafts' => 0,
        'requiresubmissionstatement' => 1,
        'sendnotifications' => 0,
        'sendlatenotifications' => 0,
        'sendstudentnotifications' => 1,
        'gradingduedate' => 0,
        'allowsubmissionsfromdate' => 0,
        'teamsubmission' => 0,
        'requireallteammemberssubmit' => 0,
        'blindmarking' => 0,
        'markingworkflow' => 0,
        'markingallocation' => 0,
    ]
);

add_activity(
    $course,
    'forum',
    'Foro: comparte y comenta una fotografia',
    '<p>Publica una fotografia o un enlace y explica que decision tecnica tomaste. Responde de forma respetuosa y constructiva a dos compañeros. No publiques datos personales ni imagenes de terceros sin permiso.</p>',
    [
        'type' => 'general',
        'assessed' => 0,
        'scale' => 0,
        'forcesubscribe' => 0,
        'trackingtype' => 1,
        'maxbytes' => 2097152,
        'grade_forum' => 0,
    ]
);

add_activity(
    $course,
    'choice',
    'Test rapido: selecciona el ajuste correcto',
    '<p>Para congelar a una persona corriendo con buena luz, ¿que velocidad elegirias?</p>',
    [
        'option' => ['1/30 s', '1/125 s', '1/1000 s'],
        'limit' => [0, 0, 0],
        'allowupdate' => 1,
        'allowmultiple' => 0,
        'showresults' => 3,
        'publish' => 0,
        'display' => 0,
        'timeopen' => 0,
        'timeclose' => 0,
    ]
);

set_config('defaulthomepage', HOMEPAGE_MY);
set_config('forcelogin', 0);
set_config('minpasswordlength', 12);
set_config('minpassworddigits', 1);
set_config('minpasswordlower', 1);
set_config('minpasswordupper', 1);
set_config('minpasswordnonalphanum', 1);
set_config('passwordpolicy', 1);
set_config('messaging', 1);
set_config('enablecompletion', 1);

mtrace('Curso FOTO101, usuarios, matriculas y cuatro actividades creados correctamente.');
