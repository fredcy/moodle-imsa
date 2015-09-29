<?php
#error_log("renderer.php called");
// This file is included magically by the moodle core.

defined('MOODLE_INTERNAL') || die();

class tool_imsa_renderer extends \plugin_renderer_base {
    public function user_ldap($data) {
        // Add field with lastaccess datetime in readable format for display.
        foreach ($data['usersa'] as $key => $user) {
            $accesstime = DateTime::createFromFormat('U', $user['lastaccess']);
            $data['usersa'][$key]['lastaccess_str'] = $accesstime->format('Y-m-d');
        }

        $out = $this->output->heading('User/LDAP');
        $out .= $this->output->render_from_template("tool_imsa/user_ldap", $data);
        #$out .= ('<pre>' . print_r($data, true) . '</pre>');
        return $out;
    }

    public function users($data) {
        $out = $this->output->heading('Selected users');
        #$out .= ('<pre>' . print_r($data, true) . '</pre>');
        $out .= $this->output->render_from_template("tool_imsa/users", $data);
        return $out;
    }
}
