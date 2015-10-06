<?php

namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once(__DIR__ . '/lib.php');

function delete_users($usernames) {
    global $DB, $USER;
    foreach ($usernames as $username) {
        $user = $DB->get_record('user', array('username'=>$username, 'deleted'=>0), '*', MUST_EXIST);
        if (is_siteadmin($user)) {
            throw new \moodle_exception('useradminodelete', 'error');
        }
        if ($USER->id == $user->id) {
            throw new \moodle_exception('usernotdeletederror', 'error');
        }
        error_log("about to delete user for username={$username}");
        user_delete_user($user);
    }
}

$usernames_s = optional_param('usernames', '', PARAM_TEXT);
$usernames = explode(',', $usernames_s);
$users = array();

$confirm_delete = optional_param('confirm-delete', '', PARAM_TEXT);
if ($confirm_delete == 'Confirm') {
    delete_users($usernames);
    redirect('user_ldap.php');
}

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

$form_id = 'confirm_form';
$PAGE->requires->js_call_amd('tool_imsa/users', 'init', array($form_id, null, true));

echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('tool_imsa');
echo $renderer->users($form_id, array('users' => $users));
echo $OUTPUT->footer();
