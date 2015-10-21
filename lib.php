<?php
namespace tool_imsa;

function js_datatables() {
    // Return javascript code that activates DataTables. I can't get this to
    // work as an "amd" module as "requirejs" is not defined there and I need
    // to set the external paths.
    return file_get_contents(__DIR__ . "/js/datatables.js");
}

$css_urls = array(
    "https://cdn.datatables.net/1.10.9/css/dataTables.bootstrap.css",
    "https://cdn.datatables.net/select/1.0.1/css/select.bootstrap.css",
);

class ldap {
    function __construct() {
        // Connect to first available LDAP server, keeping connection open for reuse.
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
        $this->cache = \cache::make('tool_imsa', 'ldapinfo');
    }

    function status($username) {
        // Return LDAP status of given username (uid) as summary string.
        $cached_status = $this->cache->get($username);
        if ($cached_status !== false) {
            return $cached_status;
        }
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
            $statusvalues = @ldap_get_values($this->link, $entry, 'organizationalstatus');
            // Some few entries are missing the attribute; deal with it. Should
            // be single value.
            $status = ($statusvalues === FALSE) ? "undefined" : $statusvalues[0];
            // Should be only one matching entry but we loop just in case.
            $entry = ldap_next_entry($this->link, $entry);
            if ($entry) {
                error_log("warning: ldap::status(): multiple entries for $username");
            }
        }
        ldap_free_result($resource);
        $this->cache->set($username, $status);
        return $status;
    }

    function close() {
        ldap_unbind($this->link);
    }
}
