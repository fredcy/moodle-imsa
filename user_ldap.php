<?php
namespace tool_imsa;
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

require_login();
require_capability('moodle/site:config', \context_system::instance());

$fields = "username,firstname,lastname,lastaccess,deleted,auth";
#$fields = "*";
$lastinitial = '';             // limit results in testing
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

class ldap {
    function __construct() {
        $config = get_config('auth/ldap');
        $hosturls = explode(';', $config->host_url);
        $bind = FALSE;
        foreach ($hosturls as $hosturl) {
            $this->link = ldap_connect($hosturl);
            $bind = @ldap_bind($this->link);
            if ($bind) {
                break;
            }
        }
        if ($bind === FALSE) {
            error_log("error: Cannot bind to LDAP servers: {$config->host_url}");
            throw new \moodle_exception("Cannot connect to LDAP servers");
        }
    }

    function status($username) {
        #error_log("ldap::status($username)");
        $basedn = 'ou=People,dc=imsa,dc=edu';
        $filter = "uid=$username";
        $attributes = array("uid", "organizationalstatus");
        $resource = ldap_search($this->link, $basedn, $filter, $attributes);
        if ($resource === FALSE) {
            return "error";
        }
        $status = 'unknown';
        $entry = ldap_first_entry($this->link, $resource);
        while ($entry) {
            $statusvalues = ldap_get_values($this->link, $entry, 'organizationalstatus');
            $status = $statusvalues[0];
            $entry = ldap_next_entry($this->link, $entry);
            if ($entry) {
                error_log("warning: ldap::status(): multiple entries for $username");
            }
        }
        ldap_free_result($resource);
        return $status;
    }

    function close() {
        ldap_unbind($this->link);
    }
}

//////////////// 

admin_externalpage_setup('course_creators');
$title = get_string('pluginname', 'tool_imsa');
$PAGE->set_title($title);       // TITLE element value in HEAD
$PAGE->set_heading($title);     // just below logo
$PAGE->requires->js_amd_inline(js_datatables());
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('tool_imsa');
$data = array('usersa' => $usersa);
echo $renderer->user_ldap($data);

echo $OUTPUT->footer();
