<?php
#error_log("renderer.php called");
// This file is included magically by the moodle core.

defined('MOODLE_INTERNAL') || die();

class tool_imsa_renderer extends \plugin_renderer_base {
    public function user_ldap($form_id, $data) {
        // Add field with lastaccess datetime in readable format for display.
        foreach ($data['usersa'] as $key => $user) {
            $accesstime = DateTime::createFromFormat('U', $user['lastaccess']);
            $data['usersa'][$key]['lastaccess_str'] = $accesstime->format('Y-m-d');
        }

        $out = $this->output->heading('Users, with LDAP OrganizationalStatus information');
        #$out .= html_writer::tag('pre', print_r($_POST, true));
        $out .= $this->output->render_from_template("tool_imsa/user_ldap", $data);
        $out .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'delete',
                                                      'value' => 'Delete selected users >>'));
        $form_attrs = array('action' => 'users.php', 'method' => 'post', 'id' => $form_id);
        $out = html_writer::tag('form', $out, $form_attrs);

        #$out .= ('<pre>' . print_r($data, true) . '</pre>');
        return $out;
    }

    public function users($form_id, $data) {
        global $CFG;
        $out = $this->output->heading('Users to be deleted');
        #$out .= html_writer::tag('pre', print_r($_POST, true));
        $out .= $this->output->render_from_template("tool_imsa/users", $data);
        $out .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'confirm-delete',
                                                      'value' => 'Delete users'));
        $out .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'cancel',
                                                      'value' => 'Cancel'));
        $form_attrs = array('method' => 'post', 'id' => $form_id);
        $out = html_writer::tag('form', $out, $form_attrs);
        return $out;
    }

    public function enrollments($form_id, $data) {
        $out = $this->output->heading("Enrollments");
        $out .= $this->output->render_from_template("tool_imsa/enrollments", $data);
        $out .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'update',
                                                      'value' => 'Update selected enrollments >>'));
        $form_attrs = array('action' => 'enrollments_update.php', 'method' => 'post', 'id' => $form_id);
        $out = html_writer::tag('form', $out, $form_attrs);

        #$out .= ('<pre>' . print_r($data, true) . '</pre>');
        return $out;
    }

    public function enrollments_update($form_id, $data, $mform) {
        $out = $this->output->heading("Enrollments Update");
        #$out .= html_writer::tag('pre', print_r($_POST, true));
        $out .= $this->output->render_from_template("tool_imsa/enrollments", $data);
        $out .= $mform->render();

        #$out .= ('<pre>' . print_r($data, true) . '</pre>');
        return $out;
    }
}
