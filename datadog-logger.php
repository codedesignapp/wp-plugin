<?php
function datadog_logger($message, $severity = 'info', $data = [], $tags = [])
{
    $datadog_api_key = '17fadf5c4b4704d6b52373537084fdf5'; // Replace with your actual Datadog API key    $hostname = gethostname();
    $datadog_endpoint = 'https://http-intake.logs.datadoghq.com/v1/input/' . $datadog_api_key;

    // Convert $data to a JSON string if it's an array or object
    $data_string = is_array($data) || is_object($data) ? json_encode($data) : $data;

    $log_entry = [
        'message' => $message,
        'severity' => $severity,
        'data' => $data_string,
        'ddsource' => 'codedesign-wordpress-plugin',
        'hostname' => gethostname(),
        'ddtags' => implode(',', $tags),
    ];

    $args = [
        'body'        => json_encode($log_entry),
        'headers'     => ['Content-Type' => 'application/json'],
        'method'      => 'POST',
        'data_format' => 'body',
    ];

    wp_remote_post($datadog_endpoint, $args);
}
