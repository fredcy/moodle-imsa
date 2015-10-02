<?php
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('course_creators');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title); // just below logo

require_login();
require_capability('moodle/site:config', context_system::instance());

echo $OUTPUT->header();
echo $OUTPUT->heading("Course creators"); // top of content area

$userfields = 'u.id, ' . get_all_user_name_fields(true, 'u');
$capability = 'moodle/course:create';
list($sort, $sortparams) = users_order_by_sql('u');
$users = get_users_by_capability(context_system::instance(), $capability, $userfields, $sort);

$users2 = array();
foreach ($users as $user) {
    $users2[] = array('firstname' => $user->firstname, 'lastname' => $user->lastname);
}
$data = array('users' => $users2);

echo $OUTPUT->render_from_template("tool_imsa/user_table", $data);
echo $OUTPUT->footer();
