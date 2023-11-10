<?php

class SettingsPage
{

    public function __construct()
    {
        // Add action hooks here
        add_action('admin_enqueue_scripts', [$this, 'enqueue_react_scripts']);
    }

    public function enqueue_react_scripts()
    {
        // This function will enqueue your React scripts and styles
        echo (SCRIPT_DEBUG);
        // if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
        //     wp_enqueue_script('mnc_settings_page_script', 'http://localhost:3001/static/js/bundle.js', [], null, true);
        // } else {
        wp_enqueue_script('mnc_settings_page_script', plugin_dir_url(__FILE__) . 'plugin-settings/build/static/js/main.js', [], null, true);
        wp_enqueue_style('mnc_settings_page_style', plugin_dir_url(__FILE__) . 'plugin-settings/build/static/css/main.css', [], null, 'all');

        // }


        $data = [
            'apiKey' => get_option('mnc_api_key'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'projectData' => get_option("cc_project_data")
        ];
        wp_localize_script('mnc_settings_page_script', 'wpData', $data);
    }

    public function render_settings_page()
    {
        // This function renders the settings page content
        echo '<div id="cd-root" className="codedesign-wrapper"></div>'; // This div will be the mount point for your React app
    }
}
