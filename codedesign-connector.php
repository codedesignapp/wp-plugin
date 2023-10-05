<?php
/*
Plugin Name: My No-Code Connector
Description: Connects WordPress to my no-code platform.
Version: 1.0
Author: Your Name
*/

// Hook to add the settings page
add_action('admin_menu', 'mnc_add_settings_page');

function mnc_add_settings_page() {
    add_options_page(
        'No-Code Connector Settings',
        'No-Code Connector',
        'manage_options',
        'mnc-settings',
        'mnc_render_settings_page'
    );
}

// Function to render the settings page
function mnc_render_settings_page() {
    // Check if API key is submitted
    if (isset($_POST['mnc_api_key'])) {
        $apiKey = sanitize_text_field($_POST['mnc_api_key']);
        if (mnc_validate_api_key($apiKey)) {
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
    echo '</p>';
    echo '</form>';
    echo '</div>';
}

function mnc_validate_api_key($apiKey) {
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
