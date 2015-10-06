<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
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

$selector = '.datatable';
$params = array("select" => true);
$params['buttons'] = array("selectAll", "selectNone");
$params['dom'] = 'Bfrtip';      // needed to position buttons; else won't display
$PAGE->requires->js_call_amd('tool_datatables/init', 'init', array($selector, $params));

$form_id = 'delete_form';
$PAGE->requires->js_call_amd('tool_imsa/user_ldap', 'init', array($form_id));

$PAGE->requires->css('/admin/tool/datatables/style/dataTables.bootstrap.css');
$PAGE->requires->css('/admin/tool/datatables/style/select.bootstrap.css');

$renderer = $PAGE->get_renderer('tool_imsa');
echo $renderer->header();
$data = array('usersa' => $usersa);
echo $renderer->user_ldap($form_id, $data);
echo $renderer->footer();
