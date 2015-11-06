<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/lib.php');

$sql = "select
coalesce(concat('enroll-', enrollment.id), concat('user-', u.id)) id
, concat(u.lastname, ', ', u.firstname) name
, u.username
-- , case u.firstaccess when 0 then 'never' else date(from_unixtime(u.firstaccess)) end firstaccess
-- , case u.lastaccess when 0 then '' else date(from_unixtime(u.lastaccess)) end lastaccess
, coalesce(concat(course.shortname, ' [', course.id, ']'), '[no-course]') coursename
, date(from_unixtime(course.startdate)) coursestart
, coalesce(date(from_unixtime(enrollment.timestart)), '') enrollstart
, coalesce(case enrollment.timeend when 0 then '' else date(from_unixtime(enrollment.timeend)) end, '') enrollend
, if (student_role.rolename is null, if (enrollment.courseid is null, '', '[no-role]'), student_role.rolename) rolename
, coalesce(date(from_unixtime(ul.timeaccess)), 'never') timeaccess
, if (enrollment.suspended is null, '', case enrollment.suspended when 1 then 'suspended' else 'active' end) suspended
, if (enrollment.enabled is null, '', case enrollment.enabled when 1 then 'disabled' else 'enabled' end) enabled
from user u
left outer join (
     select ue.id, ue.userid, enrol.courseid, ue.timestart, ue.timeend, ue.status suspended, enrol.status enabled
     from user_enrolments ue
     join enrol on ue.enrolid = enrol.id
) enrollment on u.id = enrollment.userid
left outer join (
     select ra.userid, context.instanceid courseid, role.shortname rolename
     from role_assignments ra
     join role on ra.roleid = role.id
     join context on ra.contextid = context.id and contextlevel = 50
     -- where role.shortname = 'student'
) student_role on u.id = student_role.userid and enrollment.courseid = student_role.courseid
left outer join course on enrollment.courseid = course.id
left outer join user_lastaccess ul on u.id = ul.userid and course.id = ul.courseid
where u.deleted = 0
-- and u.lastname like 'y%'
and u.username like '%@%'
order by u.lastname, u.firstname, enrollment.courseid";

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

admin_externalpage_setup('enrollments');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo

$selector = '.datatable';
$params = array("select" => true);
$params['buttons'] = array("selectAll", "selectNone");
$params['dom'] = 'Bfrtip';      // needed to position buttons; else won't display
$PAGE->requires->js_call_amd('tool_datatables/init', 'init', array($selector, $params));

$form_id = 'delete_form';
$rowselector = 'tr.selected';
$PAGE->requires->js_call_amd('tool_imsa/selections', 'init', array($form_id, $rowselector, true));

$PAGE->requires->css('/admin/tool/datatables/style/dataTables.bootstrap.css');
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
echo $renderer->enrollments($form_id, $data);
echo $renderer->footer();
