<?php
/*
Plugin Name: CodeDesign.ai Pages 
Description: Brings the power of CodeDesign.ai 
Version: 1.3.65
Author: CodeDesign.ai
*/
require_once plugin_dir_path(__FILE__) . 'settings-page.php';
require_once plugin_dir_path(__FILE__) . 'ConfigManager.php';

require_once plugin_dir_path(__FILE__) . 'plugin-update-checker-5.3/plugin-update-checker.php';
require_once(plugin_dir_path(__FILE__) . 'datadog-logger.php');


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://raw.githubusercontent.com/codedesignapp/wp-plugin/main/plugin-update.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'codedesign-plugin'
);
class CodeDesignForWordPress
{

    private $base_hostname;

    public function __construct()
    {
        // Initialize the scripts class
        new SettingsPage();
        // Other plugin actions and filters can be added here
        add_action('admin_menu', [$this, 'add_plugin_pages']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_react_app'], 99999);
        // add_action('admin_enqueue_scripts', [$this, 'mnc_enqueue_admin_scripts']);
        add_action('wp_ajax_mnc_handle_sync', [$this, 'mnc_handle_sync']);
        add_action('wp_ajax_nopriv_mnc_handle_sync', [$this, 'mnc_handle_sync']);
        add_action('wp_ajax_mnc_handle_single_page_sync', [$this, 'mnc_handle_single_page_sync']);
        add_action('wp_ajax_nopriv_mnc_handle_single_page_sync', [$this, 'mnc_handle_single_page_sync']);
        add_filter('the_content', [$this, 'replace_placeholder_with_react_root']);
        add_filter('theme_page_templates', [$this, 'mnc_add_page_template_to_dropdown']);
        add_filter('template_include', [$this, 'mnc_redirect_to_custom_template'], PHP_INT_MAX);
        add_action('wp_enqueue_scripts', [$this, 'mnc_enqueue_styles'], 999999);

        // For the AJAX validation
        add_action('wp_ajax_validate_api_key', [$this, 'validate_api_key_callback']);
        add_action('wp_ajax_nopriv_validate_api_key', [$this, 'validate_api_key_callback']);

        //For the ajax disconnect
        add_action('wp_ajax_disconnect_api_key', [$this, 'disconnect_api_key_callback']);

        //initiate rest api (webhook)
        add_action('rest_api_init', [$this, 'mnc_register_webhook_endpoint']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);


        $this->base_hostname = ConfigManager::get('base_hostname');
    }

    public function enqueue_admin_styles($hook)
    {
        error_log($hook);

        // Check if we're on the plugin's page
        if ($hook == 'toplevel_page_mnc-settings' || $hook == 'codedesign_page_codedesign-welcome') {
            wp_add_inline_style('wp-admin', '#wpfooter { display: none; }');
        }
    }


    public function mnc_register_webhook_endpoint()
    {
        register_rest_route('mnc/v1', '/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_webhook'],
            'permission_callback' => '__return_true', // Note: For security, you might want to secure this!

        ]);

        // New Webhook for disconnect
        register_rest_route('mnc/v1', '/disconnect', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_disconnect'],
            'permission_callback' => '__return_true', // Note: Secure this as well!
        ]);
    }

    public function handle_disconnect(WP_REST_Request $request)
    {
        // Authenticate the request, for example with an API key
        $apiKey = $request->get_param('api_key');
        if (!$apiKey) {
            return new WP_REST_Response(['message' => 'Invalid or missing API Key.'], 401);
        }

        // Proceed with disconnection using the existing disconnect callback
        $disconnectionResult = $this->disconnect_api_key_callback(true);

        if ($disconnectionResult) {
            return new WP_REST_Response(['message' => 'Disconnected successfully!'], 200);
        } else {
            return new WP_REST_Response(['message' => 'Error during disconnection.'], 500);
        }
    }

    public function handle_webhook(WP_REST_Request $request)
    {
        // Get the apiKey from the request data (assuming it's passed in the webhook call)
        $apiKey = $request->get_param('api_key');

        if (!$apiKey) {
            return new WP_REST_Response(['message' => 'API Key is missing.'], 400);
        }

        // Use the individual page sync method for webhook calls to avoid timeouts
        $syncResult = $this->sync_function_individual($apiKey);

        if ($syncResult) {
            return new WP_REST_Response(['message' => 'Sync completed successfully!'], 200);
        } else {
            return new WP_REST_Response(['message' => 'Error during sync.'], 500);
        }
    }



    public function mnc_redirect_to_custom_template($template)
    {
        if (is_singular('page')) {
            $assigned_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            error_log(basename($assigned_template));
            if ('full-width-template.php' == basename($assigned_template)) {
                return plugin_dir_path(__FILE__) . 'full-width-template.php';
            }
        }
        return $template;
    }


    public function mnc_add_page_template_to_dropdown($templates)
    {
        error_log("test2");

        $templates[plugin_dir_path(__FILE__) . 'full-width-template.php'] = 'Full Width Template';
        return $templates;
    }

    public function add_plugin_pages()
    {
        $svg_path = plugin_dir_path(__FILE__) . 'codedesign_logo.svg';
        // Check if the file exists before trying to encode it
        if (file_exists($svg_path)) {
            $svg_data = file_get_contents($svg_path);
            $encoded_svg = base64_encode($svg_data);
            $icon_data_uri = 'data:image/svg+xml;base64,' . $encoded_svg;
        } else {
            error_log('SVG file not found: ' . $svg_path);
            // Fallback icon
            $icon_data_uri = 'dashicons-admin-generic';
        }
        add_menu_page(
            'CodeDesign', // Page title
            'CodeDesign', // Menu title
            'manage_options', // Capability
            'mnc-settings',
            [$this, 'render_settings_page'],
            $icon_data_uri,
            6 // Position
        );

        add_submenu_page(
            'mnc-settings', // Parent slug
            'CodeDesign Plugin Welcome Page', // Page title
            'Welcome', // Menu title
            'manage_options', // Capability
            'codedesign-welcome', // Menu slug
            [$this, 'render_welcome_page'] // Callback function
        );
        add_submenu_page(
            'mnc-settings', // Parent slug
            'CodeDesign Settings', // Page title
            'Settings', // Menu title
            'manage_options', // Capability
            'codedesign-settings', // Menu slug
            [$this, 'render_settings_page'] // Callback function
        );
        remove_submenu_page('mnc-settings', 'mnc-settings');
    }

    public function render_settings_page()
    {
        // Here you can include other parts of your settings page.
        // However, if you want to display the '<div id="root"></div>' from MNC_Admin_Scripts, 
        // you can call it as shown below:
        $mnc_admin_scripts = new SettingsPage();
        $mnc_admin_scripts->render_settings_page();
    }


    public function render_welcome_page()
    {

        echo '<div id="cd-root" class="codedesign-wrapper"></div>'; // Same mount point for React app
        echo "<script>localStorage.setItem('initialPage', '/welcome');</script>";
    }



    /* Validating API Key */

    public function validate_api_key_callback()
    {
        $apiKey = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

        $result = $this->validate_api_key($apiKey);

        if ($result['valid']) {
            update_option('mnc_api_key', $apiKey);
        }

        echo json_encode($result);
        wp_die(); // This is required to terminate immediately and return a proper response
    }


    private function get_error_messages()
    {
        return [
            'ssl_error' => [
                'message' => 'SSL Certificate Error - Unable to connect securely to CodeDesign servers.',
                'suggestion' => 'This is likely a temporary issue. Please try again in a few minutes.'
            ],
            'connection_timeout' => [
                'message' => 'Connection Timeout - Unable to reach CodeDesign servers.',
                'suggestion' => 'Please check your internet connection and try again.'
            ],
            'invalid_api_key' => [
                'message' => 'Invalid API Key - The provided API key is not valid.',
                'suggestion' => 'Please check your API key in your CodeDesign project settings under Integrations.'
            ],
            'sync_failed' => [
                'message' => 'Sync Failed - API key is valid but sync process failed.',
                'suggestion' => 'Please try again. If the problem persists, contact CodeDesign support.'
            ],
            'server_error' => [
                'message' => 'Server Error - CodeDesign servers are experiencing issues.',
                'suggestion' => 'Please try again in a few minutes. If the problem persists, contact support.'
            ],
            'network_error' => [
                'message' => 'Network Error - Unable to connect to CodeDesign servers.',
                'suggestion' => 'Please check your internet connection and try again.'
            ],
            'empty_response' => [
                'message' => 'Invalid Response - Received empty or malformed response from server.',
                'suggestion' => 'Please try again. If the problem persists, contact CodeDesign support.'
            ]
        ];
    }

    public function validate_api_key($apiKey)
    {
        datadog_logger('Validating API key');
        $pathname = '/wp/validate-key';
        $endpoint = $this->base_hostname . $pathname;
        $errorMessages = $this->get_error_messages();

        // Extract the scheme (http or https) and the hostname from site_url
        $parsedUrl = parse_url(site_url());
        $scheme = $parsedUrl['scheme'];  // Will give you 'http' or 'https'
        $host = $parsedUrl['host'];      // Will give you the hostname, e.g., 'example.com'

        // Add scheme and hostname to the POST request body
        $postBody = [
            'api_key' => $apiKey,
            'scheme' => $scheme,
            'hostname' => $host
        ];

        // Send a POST request to the validation URL
        $response = wp_remote_post($endpoint, [
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($postBody),
            'timeout' => 15,  // Increase timeout to 15 seconds
            'sslverify' => false  // Disable SSL verification for development API
        ]);

        datadog_logger(print_r($response, true), "error", null, ["codedesign-wordpress-plugin-frontend"]);

        // Check the response
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            datadog_logger("Error during API key validation: " . $error_message);

            // Determine error type based on the error message
            if (strpos($error_message, 'SSL') !== false || strpos($error_message, 'certificate') !== false) {
                return [
                    'valid' => false,
                    'error_type' => 'ssl_error',
                    'error_message' => $errorMessages['ssl_error']['message'],
                    'suggestion' => $errorMessages['ssl_error']['suggestion'],
                    'raw_error' => $error_message
                ];
            } elseif (strpos($error_message, 'timeout') !== false || strpos($error_message, 'timed out') !== false) {
                return [
                    'valid' => false,
                    'error_type' => 'connection_timeout',
                    'error_message' => $errorMessages['connection_timeout']['message'],
                    'suggestion' => $errorMessages['connection_timeout']['suggestion'],
                    'raw_error' => $error_message
                ];
            } else {
                return [
                    'valid' => false,
                    'error_type' => 'network_error',
                    'error_message' => $errorMessages['network_error']['message'],
                    'suggestion' => $errorMessages['network_error']['suggestion'],
                    'raw_error' => $error_message
                ];
            }
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            return [
                'valid' => false,
                'error_type' => 'empty_response',
                'error_message' => $errorMessages['empty_response']['message'],
                'suggestion' => $errorMessages['empty_response']['suggestion'],
                'raw_error' => 'Empty response from server'
            ];
        }

        $data = json_decode($body, true);

        if ($response_code >= 500) {
            return [
                'valid' => false,
                'error_type' => 'server_error',
                'error_message' => $errorMessages['server_error']['message'],
                'suggestion' => $errorMessages['server_error']['suggestion'],
                'raw_error' => "Server returned {$response_code}: {$body}"
            ];
        }

        if (isset($data['valid']) && $data['valid']) {
            // If the API key is valid, attempt to sync
            if ($this->sync_function($apiKey)) {
                return [
                    'valid' => true,
                    'message' => 'API key validated and pages synced successfully!'
                ];
            } else {
                datadog_logger("Failed to sync after successful API key validation.");
                return [
                    'valid' => false,
                    'error_type' => 'sync_failed',
                    'error_message' => $errorMessages['sync_failed']['message'],
                    'suggestion' => $errorMessages['sync_failed']['suggestion'],
                    'raw_error' => 'Sync process failed after validation'
                ];
            }
        }

        // Invalid API key or missing 'valid' field
        return [
            'valid' => false,
            'error_type' => 'invalid_api_key',
            'error_message' => $errorMessages['invalid_api_key']['message'],
            'suggestion' => $errorMessages['invalid_api_key']['suggestion'],
            'raw_error' => $body
        ];
    }


    /* Disconnect Logic */
    /* Disconnect Logic */
    public function disconnect_api_key_callback($called_from_webhook = false)
    {
        // Remove the API key from the options table
        $apiKey = get_option('mnc_api_key');
        delete_option('mnc_api_key');

        // Remove pages and other artifacts created by the plugin
        $this->remove_plugin_artifacts();

        $backendSuccess = true; // Assume success unless we hear otherwise from the backend

        // Notify your backend only if this is not being called from the webhook
        if (!$called_from_webhook) {
            $response = $this->notify_backend_of_disconnection($apiKey);
            error_log(print_r($response, true));

            // Check if the backend notification was successful
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                $backendSuccess = isset($data['success']) && $data['success'];
            } else {
                $backendSuccess = false;
            }
        }

        echo json_encode(array('success' => $backendSuccess));
        wp_die(); // This is required to terminate immediately and return a proper response
    }


    public function notify_backend_of_disconnection($apiKey)
    {
        $pathname = '/wp/disconnect-key';
        $endpoint = $this->base_hostname . $pathname;

        $response = wp_remote_post($endpoint, [
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode(['api_key' => $apiKey]),
            'timeout' => 15  // Increase timeout to 15 seconds

        ]);


        return $response;
    }


    public function enqueue_react_app()
    {
        // Check if the current page or post contains the placeholder
        if ($this->is_page_or_post_with_placeholder()) {

            // Determine the correct hostname
            $hostname = $_SERVER['HTTP_HOST'];

            // Set the base URL based on the current hostname
            $base_url = ($hostname === 'wordpress-plugin-beta-clients.codedesign.ai') ?
                "https://wordpress-codedesign-beta.web.app/static/js/" :
                "https://wordpress-codedesign.web.app/static/js/";

            // Enqueue the React app's scripts with the determined base URL
            wp_enqueue_script('mnc-react-vendors', $base_url . 'vendors~main.chunk.js', [], null, true);
            wp_enqueue_script('mnc-react-vendors2', $base_url . 'vendors.chunk.js', [], null, true);
            wp_enqueue_script('mnc-react-app', $base_url . 'main.js', ['mnc-react-vendors', 'mnc-react-vendors2'], null, true);
            wp_enqueue_script('mnc-react-2-chunk', $base_url . '2.chunk.js', ['mnc-react-app', 'mnc-react-vendors', 'mnc-react-vendors2'], null, true);
            wp_enqueue_script('mnc-react-0-chunk', $base_url . '0.chunk.js', ['mnc-react-app', 'mnc-react-vendors', 'mnc-react-vendors2'], null, true);
            wp_enqueue_script('mnc-react-0-chunk', $base_url . '3.chunk.js', ['mnc-react-app', 'mnc-react-vendors', 'mnc-react-vendors2'], null, true);

            // Get the stored data
            $fetchedData = get_option('cc_project_data', '{}');

            // Localize the script with the data
            wp_localize_script('mnc-react-app', 'ccData', ['fetchedData' => json_decode($fetchedData, true)]);
        }
    }

    public function is_page_or_post_with_placeholder()
    {
        // Check the content of the current page or post for the placeholder string
        global $post;
        return is_a($post, 'WP_Post') && strpos($post->post_content, '[your_placeholder_string]') !== false;
    }


    public function mnc_handle_sync()
    {
        // Increase execution time for large projects
        if (function_exists('set_time_limit')) {
            set_time_limit(300); // 5 minutes
        }

        // Increase memory limit if possible
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '512M');
        }

        $fetchedData = isset($_POST['fetchedData']) ? stripslashes($_POST['fetchedData']) : '{test:"new"}';
        update_option('cc_project_data', $fetchedData);
        $pageNames = isset($_POST['pageNames']) ? json_decode(stripslashes($_POST['pageNames']), true) : [];
        update_option('mnc_page_names', $pageNames);  // Store the page names in options table

        foreach ($pageNames as $pageName) {

            //ignore linked components
            if (strpos($pageName, 'linked') === 0) {
                continue;
            }

            // Send heartbeat to prevent browser timeout
            echo " "; // Send a space character
            if (ob_get_level()) {
                ob_flush();
            }
            flush();

            // Check if a post/page with that pathname exists
            $existingPage = get_page_by_path($pageName, OBJECT, ['page', 'post']);

            // Placeholder content for the React app
            $placeholderContent = '[your_placeholder_string]';

            if ($existingPage) {
                error_log('case1 ' . $pageName);

                // Update the existing post/page
                wp_update_post([
                    'ID' => $existingPage->ID,
                    'post_content' => $placeholderContent
                ]);

                if ($pageName === 404 || $pageName === "404") {
                    error_log('Ignoring 404 page');
                    continue; // Skip this iteration and proceed with the next one
                }

                // Check if the pageName is "home" and set it as the homepage
                if ($pageName === 'home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $existingPage->ID);
                }
            } else {
                error_log('case2 ' . $pageName);

                if ($pageName === 404 || $pageName === "404") {
                    error_log('Ignoring 404 page');
                    continue; // Skip this iteration and proceed with the next one
                }
                // Create a new post/page
                $newPageID =  wp_insert_post([
                    'post_title'    => $pageName,
                    'post_name'     => $pageName,
                    'post_content'  => $placeholderContent,
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'page_template' => plugin_dir_path(__FILE__) . 'full-width-template.php'
                ]);
                if ($pageName === 'home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $newPageID);
                }
            }
        }

        wp_send_json(['success' => true]);
    }

    public function mnc_handle_single_page_sync()
    {
        $fetchedData = isset($_POST['fetchedData']) ? stripslashes($_POST['fetchedData']) : '{}';
        $pageName = isset($_POST['pageName']) ? sanitize_text_field($_POST['pageName']) : '';

        if (empty($pageName)) {
            wp_send_json(['success' => false, 'error' => 'Page name is required']);
            return;
        }

        // Ignore linked components
        if (strpos($pageName, 'linked') === 0) {
            wp_send_json(['success' => true, 'message' => 'Skipped linked component']);
            return;
        }

        // Ignore 404 pages
        if ($pageName === 404 || $pageName === "404") {
            wp_send_json(['success' => true, 'message' => 'Skipped 404 page']);
            return;
        }

        // Check if a post/page with that pathname exists
        $existingPage = get_page_by_path($pageName, OBJECT, ['page', 'post']);

        // Placeholder content for the React app
        $placeholderContent = '[your_placeholder_string]';

        try {
            if ($existingPage) {
                // Update the existing post/page
                $result = wp_update_post([
                    'ID' => $existingPage->ID,
                    'post_content' => $placeholderContent
                ]);

                if (is_wp_error($result)) {
                    wp_send_json(['success' => false, 'error' => 'Failed to update page: ' . $result->get_error_message()]);
                    return;
                }

                // Check if the pageName is "home" and set it as the homepage
                if ($pageName === 'home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $existingPage->ID);
                }

                wp_send_json(['success' => true, 'message' => 'Page updated successfully', 'page_id' => $existingPage->ID]);
            } else {
                // Create a new post/page
                $newPageID = wp_insert_post([
                    'post_title'    => $pageName,
                    'post_name'     => $pageName,
                    'post_content'  => $placeholderContent,
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'page_template' => plugin_dir_path(__FILE__) . 'full-width-template.php'
                ]);

                if (is_wp_error($newPageID)) {
                    wp_send_json(['success' => false, 'error' => 'Failed to create page: ' . $newPageID->get_error_message()]);
                    return;
                }

                if ($pageName === 'home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $newPageID);
                }

                wp_send_json(['success' => true, 'message' => 'Page created successfully', 'page_id' => $newPageID]);
            }
        } catch (Exception $e) {
            wp_send_json(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
        }
    }


    public function replace_placeholder_with_react_root($content)
    {
        return str_replace('[your_placeholder_string]', '<div id="root"></div>', $content);
    }

    public function mnc_enqueue_styles()
    {
        // Enqueue the local style
        wp_enqueue_style('mnc-custom-style', plugin_dir_url(__FILE__) . 'css/custom.css');

        // Determine the correct hostname
        $hostname = $_SERVER['HTTP_HOST'];

        // Set the base URL based on the current hostname
        $base_url = ($hostname === 'wordpress-plugin-beta-clients.codedesign.ai') ?
            "https://wordpress-codedesign-beta.web.app/assets/css/" :
            "https://wordpress-codedesign.web.app/assets/css/";

        // Enqueue the styles with the determined base URL
        wp_enqueue_style('mnc-req-styles', $base_url . "reqStyles.css");
        wp_enqueue_style('mnc-reset-styles', $base_url . "reset.css");
    }

    private function sync_function($apiKey)
    {

        $pathname = "/guest/web-builder/project?wordpress=true&bypassCache=true&returnJSON=true&key={$apiKey}";
        $endpoint = $this->base_hostname . $pathname;

        $response = wp_remote_get($endpoint, [
            'timeout' => 30  // Increased timeout for CodeDesign API calls
        ]);

        if (is_wp_error($response)) {
            datadog_logger("Error fetching page data: " . $response->get_error_message(), "error");
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $parsedResponse = json_decode($body, true);

        if (!isset($parsedResponse['data'])) {
            datadog_logger("JSON decoding failed", "error");

            return false;
        } else {
            datadog_logger("JSON decoding succeeded");
        }

        $data = $parsedResponse['data'];
        $pageNames = array_keys($data['blueprint'] ?? array());
        $logMessage = print_r($pageNames, true);

        // Log the array to the error log

        // Temporarily disable SSL verification
        add_filter('https_ssl_verify', '__return_false');
        add_filter('https_local_ssl_verify', '__return_false');


        datadog_logger($logMessage);
        $ajaxUrl = admin_url('admin-ajax.php');
        $body = array(
            'action' => 'mnc_handle_sync',
            'pageNames' => json_encode($pageNames),
            'fetchedData' => json_encode($data)
        );

        $syncResponse = wp_remote_post($ajaxUrl, array(
            'body' => $body,
            'timeout' => 60  // Increased from 15 to 60 seconds for larger projects
        ));

        // Remove the filter to re-enable SSL verification for subsequent requests
        remove_filter('https_ssl_verify', '__return_false');
        remove_filter('https_local_ssl_verify', '__return_false');

        if (is_wp_error($syncResponse)) {
            datadog_logger("Error during sync: " . $syncResponse->get_error_message());
            return false;
        }

        $syncBody = wp_remote_retrieve_body($syncResponse);
        $syncResult = json_decode($syncBody, true);

        error_log(isset($syncResult['success']) && $syncResult['success']);
        return isset($syncResult['success']) && $syncResult['success'];
    }

    private function sync_function_individual($apiKey)
    {
        $pathname = "/guest/web-builder/project?wordpress=true&bypassCache=true&returnJSON=true&key={$apiKey}";
        $endpoint = $this->base_hostname . $pathname;

        $response = wp_remote_get($endpoint, [
            'timeout' => 30  // Increased timeout for CodeDesign API calls
        ]);

        if (is_wp_error($response)) {
            datadog_logger("Error fetching page data: " . $response->get_error_message(), "error");
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $parsedResponse = json_decode($body, true);

        if (!isset($parsedResponse['data'])) {
            datadog_logger("JSON decoding failed", "error");
            return false;
        } else {
            datadog_logger("JSON decoding succeeded");
        }

        $data = $parsedResponse['data'];
        $pageNames = array_keys($data['blueprint'] ?? array());
        $logMessage = print_r($pageNames, true);

        // Temporarily disable SSL verification
        add_filter('https_ssl_verify', '__return_false');
        add_filter('https_local_ssl_verify', '__return_false');

        datadog_logger($logMessage);

        // Store the project data first
        update_option('cc_project_data', json_encode($data));
        update_option('mnc_page_names', $pageNames);

        $ajaxUrl = admin_url('admin-ajax.php');
        $successCount = 0;
        $totalPages = count($pageNames);

        // Process each page individually to avoid timeout
        foreach ($pageNames as $pageName) {
            //ignore linked components
            if (strpos($pageName, 'linked') === 0) {
                continue;
            }

            $body = array(
                'action' => 'mnc_handle_single_page_sync',
                'pageName' => $pageName,
                'fetchedData' => json_encode($data)
            );

            $syncResponse = wp_remote_post($ajaxUrl, array(
                'body' => $body,
                'timeout' => 30  // Increased timeout for individual page processing
            ));

            if (!is_wp_error($syncResponse)) {
                $syncBody = wp_remote_retrieve_body($syncResponse);
                $syncResult = json_decode($syncBody, true);

                if (isset($syncResult['success']) && $syncResult['success']) {
                    $successCount++;
                    datadog_logger("Successfully synced page: " . $pageName);
                } else {
                    datadog_logger("Failed to sync page: " . $pageName . " - " . print_r($syncResult, true), "error");
                }
            } else {
                datadog_logger("Error during individual page sync for " . $pageName . ": " . $syncResponse->get_error_message(), "error");
            }
        }

        // Remove the filter to re-enable SSL verification for subsequent requests
        remove_filter('https_ssl_verify', '__return_false');
        remove_filter('https_local_ssl_verify', '__return_false');

        // Consider it successful if at least 80% of pages were synced
        $successRate = $totalPages > 0 ? ($successCount / $totalPages) : 1;
        $isSuccessful = $successRate >= 0.8;

        if (!$isSuccessful) {
            datadog_logger("Sync partially failed. Success rate: " . ($successRate * 100) . "% ($successCount/$totalPages)", "warning");
        }

        return $isSuccessful;
    }

    public function mnc_handle_sync_optimized()
    {
        // Increase time limit for bulk processing
        if (function_exists('set_time_limit')) {
            set_time_limit(120); // 2 minutes
        }

        // Increase memory limit if possible
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '256M');
        }

        $fetchedData = isset($_POST['fetchedData']) ? stripslashes($_POST['fetchedData']) : '{test:"new"}';
        update_option('cc_project_data', $fetchedData);
        $pageNames = isset($_POST['pageNames']) ? json_decode(stripslashes($_POST['pageNames']), true) : [];
        update_option('mnc_page_names', $pageNames);

        $processedCount = 0;
        $errors = [];

        foreach ($pageNames as $pageName) {
            // Send a heartbeat every 10 pages to prevent timeout
            if ($processedCount % 10 === 0) {
                // This prevents browser/server timeouts by sending data
                echo " "; // Send a space character
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            //ignore linked components
            if (strpos($pageName, 'linked') === 0) {
                continue;
            }

            if ($pageName === 404 || $pageName === "404") {
                continue;
            }

            try {
                // Check if a post/page with that pathname exists
                $existingPage = get_page_by_path($pageName, OBJECT, ['page', 'post']);
                $placeholderContent = '[your_placeholder_string]';

                if ($existingPage) {
                    wp_update_post([
                        'ID' => $existingPage->ID,
                        'post_content' => $placeholderContent
                    ]);

                    if ($pageName === 'home') {
                        update_option('show_on_front', 'page');
                        update_option('page_on_front', $existingPage->ID);
                    }
                } else {
                    $newPageID = wp_insert_post([
                        'post_title'    => $pageName,
                        'post_name'     => $pageName,
                        'post_content'  => $placeholderContent,
                        'post_status'   => 'publish',
                        'post_type'     => 'page',
                        'page_template' => plugin_dir_path(__FILE__) . 'full-width-template.php'
                    ]);

                    if (is_wp_error($newPageID)) {
                        $errors[] = "Failed to create page '$pageName': " . $newPageID->get_error_message();
                        continue;
                    }

                    if ($pageName === 'home') {
                        update_option('show_on_front', 'page');
                        update_option('page_on_front', $newPageID);
                    }
                }

                $processedCount++;
            } catch (Exception $e) {
                $errors[] = "Error processing page '$pageName': " . $e->getMessage();
            }
        }

        $response = ['success' => true, 'processed' => $processedCount];
        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['partial_success'] = true;
        }

        wp_send_json($response);
    }

    private function remove_plugin_artifacts()
    {
        // Retrieve stored page names
        $storedPageNames = get_option('mnc_page_names', []);

        // Remove each stored page
        foreach ($storedPageNames as $pageName) {
            $page = get_page_by_title($pageName);
            if ($page) {
                wp_delete_post($page->ID, true);
            }
        }

        // Delete the stored page names option
        delete_option('mnc_page_names');

        // Remove other artifacts here...
        // Depending on what other artifacts your plugin creates (e.g., custom post types, taxonomies, meta data),
        // you will want to delete or remove those here.
    }
}

// Initialize the plugin
new CodeDesignForWordPress();
