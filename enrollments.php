<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/lib.php');

require_login();
admin_externalpage_setup('enrollments');

// We will display some page elements only if the user is allowed to update enrollments.
$allowedToUpdate = has_capability('moodle/site:config', \context_system::instance());

$sql = file_get_contents("enrollments.sql");

$result = $DB->get_records_sql($sql, array());

$enrollments = array();
foreach ($result as $r) {
    $e = array('id' => $r->id,
               'name' => $r->name,
               'username' => $r->username,
               'coursename'=> $r->coursename,
               'coursestart' => $r->coursestart,
               'enrollstart' => $r->enrollstart,
               'enrollend' => $r->enrollend,
               'timeaccess' => $r->timeaccess,
    );
    $enrollments[] = $e;
}

//////////////// 

$PAGE->set_pagelayout('report');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo

$selector = '.datatable';
if ($allowedToUpdate) {
    $params = array("select" => true);
    $params['buttons'] = array("selectAll", "selectNone");
    $params['dom'] = 'Bfrtip';      // needed to position buttons; else won't display
} else {
    $params = array();
}
$PAGE->requires->js_call_amd('tool_datatables/init', 'init', array($selector, $params));

$form_id = 'select_form';       // arbitrary name but used in multiple places
$rowselector = 'tr.selected';
$PAGE->requires->js_call_amd('tool_imsa/selections', 'init', array($form_id, $rowselector, true));

#$PAGE->requires->css('/admin/tool/datatables/style/dataTables.bootstrap.css');
$PAGE->requires->css('/admin/tool/datatables/style/select.bootstrap.css');

$renderer = $PAGE->get_renderer('tool_imsa');
echo $renderer->header();
if (! empty($SESSION->alerts)) {
    foreach ($SESSION->alerts as $alert) {
        echo $renderer->notification($alert[0], $alert[1]);
    }
    unset($SESSION->alerts);
}
$data = array('enrollments' => $enrollments, 'result' => $result);
echo $renderer->enrollments($form_id, $data, $allowedToUpdate);
echo $renderer->footer();
