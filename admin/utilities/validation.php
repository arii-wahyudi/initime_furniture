<?php

/**
 * Input validation utilities
 * Centralizes common validation logic
 */

/**
 * Check and validate CSRF token
 * Dies on failure
 * @param string|null $token CSRF token from POST
 * @return void
 */
function require_valid_csrf($token = null)
{
    $token = $token ?? ($_POST['csrf'] ?? '');
    if (!check_csrf($token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}

/**
 * Validate POST request method
 * Redirects on failure
 * @param string $redirect_url URL to redirect to on failure
 * @return void
 */
function require_post_method($redirect_url)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Get integer ID from request
 * @param string $param Parameter name (GET or POST)
 * @param int $default Default value
 * @return int
 */
function get_id($param = 'id', $default = 0)
{
    $value = $_GET[$param] ?? $_POST[$param] ?? $default;
    return (int)$value;
}

/**
 * Get and trim string from POST
 * @param string $key POST key
 * @param string $default Default value
 * @return string
 */
function get_post_string($key, $default = '')
{
    return trim((string)($_POST[$key] ?? $default));
}

/**
 * Get integer from POST
 * @param string $key POST key
 * @param int $default Default value
 * @return int
 */
function get_post_int($key, $default = 0)
{
    return (int)($_POST[$key] ?? $default);
}

/**
 * Get float from POST
 * @param string $key POST key
 * @param float $default Default value
 * @return float
 */
function get_post_float($key, $default = 0.0)
{
    return floatval($_POST[$key] ?? $default);
}

/**
 * Get boolean from POST
 * @param string $key POST key
 * @return bool
 */
function get_post_bool($key)
{
    return !empty($_POST[$key]);
}

/**
 * Validate required fields in POST data
 * Dies if any field is missing
 * @param array $fields Field names to validate
 * @return void
 */
function require_post_fields($fields)
{
    foreach ((array)$fields as $field) {
        if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
            http_response_code(400);
            die("Kolom '$field' diperlukan");
        }
    }
}
