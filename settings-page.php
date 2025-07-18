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
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            wp_enqueue_script('mnc_settings_page_script', 'http://localhost:3000/static/js/bundle.js', [], null, true);
        } else {
            wp_enqueue_script('mnc_settings_page_script', plugin_dir_url(__FILE__) . 'plugin-settings/build/static/js/main.js', [], null, true);
            wp_enqueue_style('mnc_settings_page_style', plugin_dir_url(__FILE__) . 'plugin-settings/build/static/css/main.css', [], null, 'all');
        }


        $data = [
            'apiKey' => get_option('mnc_api_key'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'projectData' => $this->get_safe_project_data()
        ];
        wp_localize_script('mnc_settings_page_script', 'wpData', $data);
    }

    private function get_safe_project_data()
    {
        $projectData = get_option("cc_project_data");

        // Validate that project data is valid JSON
        if (!empty($projectData)) {
            $decoded = json_decode($projectData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Ensure required structure exists
                if (!isset($decoded['blueprint'])) {
                    $decoded['blueprint'] = [];
                }
                if (!isset($decoded['timestamps'])) {
                    $decoded['timestamps'] = ['updatedAt' => null];
                }
                return json_encode($decoded);
            }
        }

        // Return safe fallback JSON if data is corrupt or missing
        return json_encode([
            'blueprint' => [],
            'timestamps' => ['updatedAt' => null]
        ]);
    }

    public function render_settings_page()
    {
        // This function renders the settings page content
        echo '<div id="cd-root" className="codedesign-wrapper"></div>'; // This div will be the mount point for your React app
    }
}
