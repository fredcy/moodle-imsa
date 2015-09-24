<?php
#error_log("admin/imsa/settings.php");
defined('MOODLE_INTERNAL') || die;
if (! $hassiteconfig)
    return;

// Group our IMSA pages under a new admin category (nested section of admin menu)
$category = new admin_category('imsa', 'IMSA tools');

// Put the new pages into that category. The name values below (first arg) have
// to match the name values in the admin_externalpage_setup() calls for the
// various pages.
$category->add('imsa', new admin_externalpage('course_creators', "Course creators",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/index.php"));
$category->add('imsa', new admin_externalpage('user_ldap', "User/LDAP connection",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/user_ldap.php"));

// Link the category itself into the admin menu structure
$ADMIN->add('reports', $category);

