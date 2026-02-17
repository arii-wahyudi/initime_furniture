<?php

/**
 * Response and redirect utilities
 * Centralizes message passing and redirects
 */

/**
 * Redirect with optional message
 * @param string $url URL to redirect to
 * @param string $type Message type: 'success', 'error', 'info'
 * @param string $message Message text
 * @return void
 */
function redirect_with_message($url, $type = 'success', $message = '')
{
    if ($message) {
        $_SESSION['message'] = [
            'type' => $type,
            'text' => $message
        ];
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Simple redirect
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Get and clear message from session
 * @return array|null Message array with 'type' and 'text' keys, or null
 */
function get_session_message()
{
    $message = $_SESSION['message'] ?? null;
    unset($_SESSION['message']);
    return $message;
}

/**
 * Display alert HTML based on session message
 * @return string HTML alert, or empty string
 */
function render_alert()
{
    $message = get_session_message();
    if (!$message) {
        return '';
    }

    $type = $message['type'];
    $text = htmlspecialchars($message['text']);

    $alerts = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'info' => 'alert-info',
        'warning' => 'alert-warning'
    ];

    $class = $alerts[$type] ?? 'alert-info';

    return "<div class=\"alert {$class} alert-dismissible fade show\" role=\"alert\">" .
        $text .
        "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>" .
        "</div>";
}
