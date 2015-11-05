<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/lib.php');

require_login();
$PAGE->set_context(\context_system::instance());
$url = new \moodle_url("/admin/tool/imsa/enrollments_update.php");
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo
admin_externalpage_setup('enrollments_update');

$form_id = 'update_form';

class enrollment_update_form extends \moodleform {
    function definition() {
        $mform =& $this->_form;
        $mform->addElement('date_selector', 'enrollend', 'New enrollment end');
        $mform->addElement('hidden', 'selections', '');
        $mform->setType('selections', PARAM_RAW);
        $this->add_action_buttons();
    }
}

$attributes = array('id' => $form_id);
$mform = new enrollment_update_form(null, null, 'post', '', $attributes);

$selections_s = optional_param('selections', '', PARAM_TEXT);
$enroll_ids = array();
foreach (explode(',', $selections_s) as $selection) {
    if ($selection == '') {
        continue;
    }
    list($type, $id) = explode('-', $selection);
    if ($type == "enroll") {
        $enroll_ids[] = $id;
    }
    // else ignore other values, "user-NNN" in particular
}


if ($mform->is_cancelled()) {
    error_log("form cancelled");
} else if ($from_form = $mform->get_data()) {
    // Form submitted, so process it.
    // We already got $enroll_ids from the 'selections' above; don't need to get from $from_form again.

    // Update the enrollment 'timeend' values to the new date.
    $successful = array();
    $failed = array();
    foreach ($enroll_ids as $id) {
        $data = array('id' => $id, 'timeend' => $from_form->enrollend);
        $ret = $DB->update_record('user_enrolments', $data);
        if ($ret === true) {
            $successful[] = $id;
        } else {
            $failed[] = $id;
        }
    }

    // Report how that went.
    $date_str = date("Y-m-d", $from_form->enrollend);
    $SESSION->alerts = array();
    $okcount = count($successful);
    if ($okcount > 0) {
        $SESSION->alerts[] = array("Updated {$okcount} enrollments to end on {$date_str}", 'notifysuccess');
    }
    $failcount = count($failed);
    if ($failcount > 0) {
        $SESSION->alerts[] = array("Failed to update {$failcount} enrollments", 'notifyproblem');
    }
}


// generate SQL "in" clause
if (empty($enroll_ids)) {
    list($insql, $params) = array("is null", array()); // won't select anything
} else {
    list($insql, $params) = $DB->get_in_or_equal($enroll_ids);
}

$sql = "select
concat('enroll-', ue.id) as id
, concat(user.lastname, ', ', user.firstname) as name
, user.username
, course.shortname as coursename
, date(from_unixtime(course.startdate)) as coursestart
, date(from_unixtime(ue.timestart)) as enrollstart
, case ue.timeend when 0 then '' else date(from_unixtime(ue.timeend)) end as enrollend
, date(from_unixtime(ul.timeaccess)) as timeaccess
from user_enrolments ue
join user on ue.userid = user.id
join enrol on ue.enrolid = enrol.id
join course on enrol.courseid = course.id
left outer join user_lastaccess ul on ue.userid = ul.userid
where ue.id {$insql}
order by user.lastname, user.firstname, course.id
";

$result = $DB->get_records_sql($sql, $params);

// Convert from array-of-objects form returned from SQL API to array-of-arrays
// form needed by templates.
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

// Set up DataTables functions on the table of enrollments
$selector = '.datatable';
$params = array();
$PAGE->requires->js_call_amd('tool_datatables/init', 'init', array($selector, $params));
$PAGE->requires->css('/admin/tool/datatables/style/dataTables.bootstrap.css');

// Set up onSubmit hook that puts the table row ids into the 'selections' form variable.
$rowselector = '.datatable tbody tr';
$PAGE->requires->js_call_amd('tool_imsa/selections', 'init', array($form_id, $rowselector, true));

// Start rendering the output page.
$renderer = $PAGE->get_renderer('tool_imsa');
echo $renderer->header();

// Display any alerts
if (! empty($SESSION->alerts)) {
    foreach ($SESSION->alerts as $alert) {
        echo $renderer->notification($alert[0], $alert[1]);
    }
    unset($SESSION->alerts);
}

// Display the main content: table of enrollments and form for updating the date.
$data = array('enrollments' => $enrollments, 'result' => $result);
echo $renderer->enrollments_update($form_id, $data, $mform);

echo $renderer->footer();
