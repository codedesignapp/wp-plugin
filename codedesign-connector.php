<?php
/*
Plugin Name: My No-Code Connector
Description: Connects WordPress to my no-code platform.
Version: 1.0
Author: Your Name
*/

class MyNoCodeConnector {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_react_app']);
        add_action('admin_enqueue_scripts', [$this,'mnc_enqueue_admin_scripts']);
        add_action('wp_ajax_mnc_handle_sync', [$this, 'mnc_handle_sync']);
        add_action('wp_ajax_nopriv_mnc_handle_sync', [$this, 'mnc_handle_sync']);
        add_filter('the_content', [$this, 'replace_placeholder_with_react_root']);

    }

    public function add_settings_page() {
        add_options_page(
            'No-Code Connector Settings',
            'No-Code Connector',
            'manage_options',
            'mnc-settings',
            [$this, 'render_settings_page']
        );
    }

   public function mnc_enqueue_admin_scripts() {
    wp_enqueue_script('cc-helpers-js', plugin_dir_url(__FILE__) . 'helpers.js', ['jquery'], '1.0.0', true);

    // Pass ajax_url to script.js
    wp_localize_script('cc-helpers-js', 'mnc_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }


    public function render_settings_page() {
        // Check if API key is submitted
        if (isset($_POST['mnc_api_key'])) {
            $apiKey = sanitize_text_field($_POST['mnc_api_key']);
            if ($this->validate_api_key($apiKey)) {
                update_option('mnc_api_key', $apiKey);
                echo '<div class="updated"><p>API Key validated and saved!</p></div>';
            } else {
                echo '<div class="error"><p>Invalid API Key!</p></div>';
            }
        }

        // Get the current API key
        $currentApiKey = get_option('mnc_api_key', '');

        // Render the form
        echo '<div class="wrap">';
        echo '<h2>No-Code Connector Settings</h2>';
        echo '<form method="post" action="">';
        echo '<table class="form-table">';
        echo '<tr valign="top">';
        echo '<th scope="row">API Key</th>';
        echo '<td><input type="text" name="mnc_api_key" value="' . esc_attr($currentApiKey) . '" class="regular-text" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '<p class="submit">';
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">';

        // Display the "Sync" button only if the API key is validated
        if ($this->validate_api_key($currentApiKey)) {
            echo ' <input type="button" name="sync" id="sync" class="button" value="Sync" onclick="syncFunction()">';
        }

        echo '</p>';
        echo '</form>';
        echo '</div>';
    }

    public function validate_api_key($apiKey) {
        // Append the API key as a query parameter to the validation URL
        $url = 'http://20.40.53.151:3000/wp/validate-key?api_key=' . urlencode($apiKey);

        // Send a GET request to the validation URL
        $response = wp_remote_get($url);

        // Check the response
        if (is_wp_error($response)) {
            return false; // Handle the error appropriately
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return isset($data['valid']) && $data['valid'];
    }

    public function enqueue_react_app() {
        // Check if the current page or post contains the placeholder
        if ($this->is_page_or_post_with_placeholder()) {
            // Enqueue the React app's main.js and vendors~main.js files
            wp_enqueue_script('mnc-react-vendors', plugin_dir_url(__FILE__) . 'build/static/js/vendors~main.chunk.js', [], null, true);
            wp_enqueue_script('mnc-react-app', plugin_dir_url(__FILE__) . 'build/static/js/main.js', ['mnc-react-vendors'], null, true);
        }
    }

   public function is_page_or_post_with_placeholder() {
    // Check the content of the current page or post for the placeholder string
    global $post;
    return is_a($post, 'WP_Post') && strpos($post->post_content, '[your_placeholder_string]') !== false;
}
    public function mnc_handle_sync() {

        $pageNames = isset($_POST['pageNames']) ? json_decode(stripslashes($_POST['pageNames']), true) : [];

        foreach ($pageNames as $pageName) {
            // Check if a post/page with that pathname exists
            $existingPage = get_page_by_path($pageName, OBJECT, ['page', 'post']);

            // Placeholder content for the React app
            $placeholderContent = '[your_placeholder_string]';

            if ($existingPage) {
                // Update the existing post/page
                wp_update_post([
                    'ID' => $existingPage->ID,
                    'post_content' => $placeholderContent
                ]);
            } else {
                // Create a new post/page
                wp_insert_post([
                    'post_title' => $pageName,
                    'post_name' => $pageName,
                    'post_content' => $placeholderContent,
                    'post_status' => 'publish',
                    'post_type' => 'page' // or 'post' depending on your needs
                ]);
            }
        }

        wp_send_json(['success' => true]);
    }


    public function replace_placeholder_with_react_root($content) {
        return str_replace('[your_placeholder_string]', '<div id="root"></div>', $content);
    }
}

// Initialize the plugin
new MyNoCodeConnector();


