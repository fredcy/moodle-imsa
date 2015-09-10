<?php

error_log("admin/imsa/index.php");

require(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('toolimsa');

echo $OUTPUT->header();
echo "<h2>Hello from admin/tool/imsa</h2>";
echo $OUTPUT->footer();
