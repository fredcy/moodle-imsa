<?php
defined('MOODLE_INTERNAL') || die;

// Group our IMSA pages under a new admin category (nested section of admin menu)
$category = new admin_category('imsa', 'IMSA tools');

// Put the new pages into that category. The name values below (first arg) have
// to match the name values in the admin_externalpage_setup() calls for the
// various pages.
$category->add('imsa', new admin_externalpage('course_creators', "Course creators",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/course_creators.php"));

$category->add('imsa', new admin_externalpage('user_ldap', "User/LDAP info",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/user_ldap.php"));

$category->add('imsa', new admin_externalpage('imsa_users', "IMSA user list",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/users.php",
                                              'moodle/site:config', true));

$category->add('imsa', new admin_externalpage('enrollments', "Enrollments",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/enrollments.php",
                                              'enrol/manual:manage'));

$category->add('imsa', new admin_externalpage('enrollments_update', "Enrollments Update",
                                              "$CFG->wwwroot/$CFG->admin/tool/imsa/enrollments_update.php",
                                              'moodle/site:config', true));

// Link the category itself into the admin menu structure
$ADMIN->add('reports', $category);


