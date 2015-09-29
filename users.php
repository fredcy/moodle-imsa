<?php

namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

$usernames_s = optional_param('usernames', '', PARAM_TEXT);
$usernames = explode(',', $usernames_s);
$users = array();
$fields = 'id, username, firstname, lastname, lastaccess, auth';
foreach ($usernames as $username) {
    $params = array('username' => $username);
    $records = $DB->get_records_sql("select $fields from user where username=:username", $params);
    $users = array_merge($users, $records);
}

//////////////////

admin_externalpage_setup('imsa_users');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo
$PAGE->requires->js_amd_inline(js_datatables());
foreach ($css_urls as $url) {
    $PAGE->requires->css(new \moodle_url($url));
}
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('tool_imsa');
echo $renderer->users(array('users' => $users));
echo $OUTPUT->footer();
