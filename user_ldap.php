<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

$fields = "username,firstname,lastname,lastaccess,deleted,auth";
$lastinitial = 'k';             // limit results in testing
$users = get_users(true, '', false, array(), 'lastname ASC',
                   '', $lastinitial, '', '', $fields);
$usersa = array();
$ld = new ldap();
foreach ($users as $user) {
    $u = array('username'   => $user->username,
               'firstname'  => $user->firstname,
               'lastname'   => $user->lastname,
               'lastaccess' => $user->lastaccess,
               'auth'       => $user->auth,
               'status'     => $ld->status($user->username));
    $usersa[] = $u;
}
$ld->close();

//////////////// 

admin_externalpage_setup('course_creators');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo
$PAGE->requires->js_amd_inline(js_datatables());
foreach ($css_urls as $url) {
    $PAGE->requires->css(new \moodle_url($url));
}
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('tool_imsa');
$data = array('usersa' => $usersa);
echo $renderer->user_ldap($data);
echo $OUTPUT->footer();
