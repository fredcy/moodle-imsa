<?php
error_log("tool/imsa/db/cache.php evaluated");

defined('MOODLE_INTERNAL') || die;

$definitions = array(
    'ldapinfo' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simpledata' => true,   // status string
        'ttl' => 3600           // seconds
    )
);
