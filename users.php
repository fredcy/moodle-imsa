<?php

namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once(__DIR__ . '/lib.php');

function delete_users($usernames) {
    global $DB, $USER;
    $successful = array();
    $failed = array();
    foreach ($usernames as $username) {
        $user = $DB->get_record('user', array('username'=>$username, 'deleted'=>0), '*', MUST_EXIST);
        if (is_siteadmin($user)) {
            throw new \moodle_exception('useradminodelete', 'error');
        }
        if ($USER->id == $user->id) {
            throw new \moodle_exception('usernotdeletederror', 'error');
        }
        error_log("about to delete user for username={$username}");
        try {
            user_delete_user($user);
            $successful[] = $username;
        } catch (\Exception $e) {
            $info = get_exception_info($e);
            error_log("info = " . print_r($info, true));
            error_log("Exception while deleting {$username}: " . $e->getCode() . ': ' . $e->getMessage());
            error_log($e->getTraceAsString());
            $failed[] = $username;
        }
    }
    return array($successful, $failed);
}

$usernames_s = optional_param('usernames', '', PARAM_TEXT);
$usernames = explode(',', $usernames_s);
$users = array();

$confirm_delete = optional_param('confirm-delete', '', PARAM_TEXT);
$cancel = optional_param('cancel', '', PARAM_TEXT);
if ($confirm_delete == 'Delete users') {
    list($successful, $failed) = delete_users($usernames);
    $SESSION->alerts = array();
    $okcount = count($successful);
    if ($okcount > 0) {
        $message = "Deleted $okcount users with these usernames: " . implode(', ', $successful);
        $SESSION->alerts[] = array($message, 'notifysuccess');
    }
    $failcount = count($failed);
    if ($failcount > 0) {
        $message = "Failed to delete $failcount users with these usernames: " . implode(', ', $failed);
        $SESSION->alerts[] = array($message, 'notifyproblem');
    }
    redirect('user_ldap.php');
} else if ($cancel) {
    $SESSION->alerts = array(array('Cancelled; no users deleted', 'notifymessage'));
    redirect('user_ldap.php');
}
// else carry on and display the page

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
