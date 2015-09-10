<?php

error_log("admin/imsa/settings.php");

$ADMIN->add('reports', new admin_externalpage('toolimsa', "IMSA tools",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/index.php"));
