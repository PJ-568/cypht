<?php

/* INPUT */

if (!class_exists('HM_Handler_http_headers')) {
class Hm_Handler_http_headers extends Hm_Handler_Module {
    public function process($data) {
        if (isset($data['language'])) {
            $data['http_headers'][] = 'Content-Language: '.substr($data['language'], 0, 2);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_process_language_setting')) {
class Hm_Handler_process_language_setting extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings', 'language_setting'));
        if ($success) {
            $data['new_user_settings']['language_setting'] = $form['language_setting'];
        }
        else {
            $data['user_settings']['language'] = $this->user_config->get('language_setting', false);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_process_timezone_setting')) {
class Hm_Handler_process_timezone_setting extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings', 'timezone_setting'));
        if ($success) {
            $data['new_user_settings']['timezone_setting'] = $form['timezone_setting'];
        }
        else {
            $data['user_settings']['timezone'] = $this->user_config->get('timezone_setting', false);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_save_user_settings')) {
class Hm_Handler_save_user_settings extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings'));
        if ($success && isset($data['new_user_settings'])) {
            foreach ($data['new_user_settings'] as $name => $value) {
                $this->user_config->set($name, $value);
            }
            $user = $this->session->get('username', false);
            $path = $this->config->get('user_settings_dir', false);
            if ($user && $path) {
                $this->user_config->save($user);
                Hm_Msgs::add('Settings saved');
            }
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_title')) {
class Hm_Handler_title extends Hm_Handler_Module {
    public function process($data) {
        $data['title'] = ucfirst($this->page);
        return $data;
    }
}}

if (!class_exists('Hm_Handler_language')) {
class Hm_Handler_language extends Hm_Handler_Module {
    public function process($data) {
        $data['language'] = $this->user_config->get('language_setting', 'en_US');
        //$data['language'] = $this->session->get('language', 'en_US');
        return $data;
    }
}}

if (!class_exists('Hm_Handler_date')) {
class Hm_Handler_date extends Hm_Handler_Module {
    public function process($data) {
        $data['date'] = date('Y-m-d h:i:s');
        return $data;
    }
}}

if (!class_exists('Hm_Handler_login')) {
class Hm_Handler_login extends Hm_Handler_Module {
    public function process($data) {
        if (!isset($this->request->post['create_hm_user'])) {
            list($success, $form) = $this->process_form(array('username', 'password'));
            if ($success) {
                $this->session->check($this->request, $form['username'], $form['password']);
                $this->session->set('username', $form['username']);
            }
            else {
                $this->session->check($this->request);
            }
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_create_user')) {
class Hm_Handler_create_user extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('username', 'password', 'create_hm_user'));
        if ($success) {
            $this->session->create($this->request, $form['username'], $form['password']);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_load_user_data')) {
class Hm_Handler_load_user_data extends Hm_Handler_Module {
    public function process($data) {
        $user_data = $this->session->get('user_data', array());
        if (!empty($user_data)) {
            $this->user_config->reload($user_data);
        }
        else {
            $user = $this->session->get('username', false);
            $this->user_config->load($user);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_save_user_data')) {
class Hm_Handler_save_user_data extends Hm_Handler_Module {
    public function process($data) {
        $user_data = $this->user_config->dump();
        if (!empty($user_data)) {
            $this->session->set('user_data', $user_data);
        }
        return $data;
    }
}}

if (!class_exists('Hm_Handler_logout')) {
class Hm_Handler_logout extends Hm_Handler_Module {
    public function process($data) {
        if (isset($this->request->post['logout']) && !$this->session->loaded) {
            $user = $this->session->get('username', false);
            $path = $this->config->get('user_settings_dir', false);
            if ($user && $path) {
                $this->user_config->save($user);
                Hm_Msgs::add('saved user data on logout');
            }
            $this->session->destroy();
            Hm_Msgs::add('session destroyed on logout');
        }
        return $data;
    }
}}

/* OUTPUT */

if (!class_exists('Hm_Output_title')) {
class Hm_Output_title extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<h1 class="title">HM3</h1>';
        }
    }
}}

if (!class_exists('Hm_Output_login')) {
class Hm_Output_login extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            if (!$input['router_login_state']) {
                $res = '<form class="login_form" method="POST">'.
                    ' '.$this->trans('Username').': <input type="text" name="username" value="">'.
                    ' '.$this->trans('Password').': <input type="password" name="password">'.
                    ' <input type="submit" value="Login" />';
                if ($input['router_page_name'] == 'home') {
                    $res .= ' <input type="submit" name="create_hm_user" value="Create" />';
                }
                $res .= '</form>';
                return $res;
            }
        }
        return '';
    }
}}

if (!class_exists('Hm_Output_date')) {
class Hm_Output_date extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<div class="date">'.$this->html_safe($input['date']).'</div>';
        }
    }
}}

if (!class_exists('Hm_Output_logout')) {
class Hm_Output_logout extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' && $input['router_login_state']) {
            return '<form class="logout_form" method="POST"><input type="submit" class="logout" name="logout" value="Logout" /></form>';
        }
    }
}}

if (!class_exists('Hm_Output_msgs')) {
class Hm_Output_msgs extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            $res = '';
            $msgs = Hm_Msgs::get();
            $res .= '<div class="sys_messages">';
            if (!empty($msgs)) {
                foreach ($msgs as $val) {
                    $res .= $this->html_safe($val).'<br />';
                }
            }
            $res .= '</div>';
            return $res;
        }
        return '';
    }
}}

if (!class_exists('Hm_Output_header_start')) {
class Hm_Output_header_start extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $lang = '';
            if ($this->lang) {
                $lang = 'lang='.strtolower(str_replace('_', '-', $this->lang));
            }
            return '<!DOCTYPE html><html '.$lang.'><head>';
        }
        elseif ($format == 'CLI') {
            return sprintf("\nHM3 CLI Interface\n\n");
        }
    }
}}

if (!class_exists('Hm_Output_header_end')) {
class Hm_Output_header_end extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '</head><body>';
        }
    }
}}

if (!class_exists('Hm_Output_header_content')) {
class Hm_Output_header_content extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<title>HM3</title><meta charset="utf-8" />';
        }
    }
}}

if (!class_exists('Hm_Output_settings_link')) {
class Hm_Output_settings_link extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<a class="settings_link" href="'.$this->html_safe($input['router_url_path']).'?page=settings">'.$this->trans('Settings').'</a>';
        }
    }
}}

if (!class_exists('Hm_Output_homepage_link')) {
class Hm_Output_homepage_link extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<a class="home_link" href="'.$this->html_safe($input['router_url_path']).'">'.$this->trans('Home').'</a>';
        }
    }
}}

if (!class_exists('Hm_Output_header_css')) {
class Hm_Output_header_css extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $res = '';
            if (DEBUG_MODE) {
                foreach (glob('modules/*', GLOB_ONLYDIR | GLOB_MARK) as $name) {
                    if (is_readable(sprintf("%ssite.css", $name))) {
                        $res .= '<link href="'.sprintf("%ssite.css", $name).'" media="all" rel="stylesheet" type="text/css" />';
                    }
                }
            }
            else {
                $res .= '<link href="site.css" media="all" rel="stylesheet" type="text/css" />';
            }
            return $res;
        }
    }
}}

if (!class_exists('Hm_Output_page_js')) {
class Hm_Output_page_js extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            if (DEBUG_MODE) {
                $res = '';
                foreach (glob('modules/*', GLOB_ONLYDIR | GLOB_MARK) as $name) {
                    if (is_readable(sprintf("%ssite.js", $name))) {
                        $res .= '<script type="text/javascript" src="'.sprintf("%ssite.js", $name).'"></script>';
                    }
                }
                return $res;
            }
            else {
                return '<script type="text/javascript" src="site.js"></script>';
            }
        }
    }
}}

if (!class_exists('Hm_Output_footer')) {
class Hm_Output_footer extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '</body></html>';
        }
    }
}}

if (!class_exists('Hm_Output_jquery')) {
class Hm_Output_jquery extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<script type="text/javascript" src="modules/core/jquery-1.11.0.min.js"></script>';
        }
        return '';
    }
}}

if (!class_exists('Hm_Output_js_data')) {
class Hm_Output_js_data extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<script type="text/javascript">'.
                'var hm_url_path = "'.$input['router_url_path'].'";'.
                'var hm_page_name = "'.$input['router_page_name'].'";'.
                '</script>';
        }
    }
}}

if (!class_exists('Hm_Output_loading_icon')) {
class Hm_Output_loading_icon extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="loading_icon"><img alt="Loading..." src="images/ajax-loader.gif" width="16" height="16" /></div>';
        }
    }
}}

if (!class_exists('Hm_Output_start_settings_form')) {
class Hm_Output_start_settings_form extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="user_settings"><div class="subtitle">Settings</div><form method="POST" action=""><table>';
        }
    }
}}

if (!class_exists('Hm_Output_language_setting')) {
class Hm_Output_language_setting extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $langs = array(
                'en_US' => 'English',
                'es_ES' => 'Spanish'
            );
            if (isset($input['user_settings']['language'])) {
                $mylang = $input['user_settings']['language'];
            }
            else {
                $mylang = false;
            }
            $res = '<tr><td>Interface Language</td><td><select name="language_setting">';
            foreach ($langs as $id => $lang) {
                $res .= '<option ';
                if ($id == $mylang) {
                    $res .= 'selected="selected" ';
                }
                $res .= 'value="'.$id.'">'.$lang.'</option>';
            }
            $res .= '</select></td></tr>';
            return $res;
        }
    }
}}

if (!class_exists('Hm_Output_timezone_setting')) {
class Hm_Output_timezone_setting extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $zones = timezone_identifiers_list();
            if (isset($input['user_settings']['timezone'])) {
                $myzone = $input['user_settings']['timezone'];
            }
            else {
                $myzone = false;
            }
            $res = '<tr><td>Timezone</td><td><select name="timezone_setting">';
            foreach ($zones as $zone) {
                $res .= '<option ';
                if ($zone == $myzone) {
                    $res .= 'selected="selected" ';
                }
                $res .= 'value="'.$zone.'">'.$zone.'</option>';
            }
            $res .= '</select></td></tr>';
            return $res;
        }
    }
}}

if (!class_exists('Hm_Output_end_settings_form')) {
class Hm_Output_end_settings_form extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<tr><td colspan="2" class="submit_cell">'.
                '<input class="save_settings" type="submit" name="save_settings" value="Save" />'.
                '</tr></table></form></div>';
        }
    }
}}

if (!class_exists('Hm_Output_toolbar_start')) {
class Hm_Output_toolbar_start extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="toolbar">';
        }
    }
}}

if (!class_exists('Hm_Output_toolbar_end')) {
class Hm_Output_toolbar_end extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '</div>';
        }
    }
}}

?>
