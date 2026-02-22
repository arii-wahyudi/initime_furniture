<?php
/**
 * Simple logging system untuk debug dan error tracking
 */

define('LOG_DIR', __DIR__ . '/../../logs');
define('LOG_FILE_ERROR', LOG_DIR . '/error.log');
define('LOG_FILE_DEBUG', LOG_DIR . '/debug.log');

// Create logs directory jika belum ada
if (!is_dir(LOG_DIR)) {
    @mkdir(LOG_DIR, 0755, true);
}

/**
 * Simpan error ke log file
 */
function log_error($message, $context = [])
{
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $user = $_SESSION['admin_id'] ?? 'UNKNOWN';
    
    $log_context = '';
    if (!empty($context)) {
        $log_context = ' | Context: ' . json_encode($context);
    }
    
    $log_message = "[{$timestamp}] [ERROR] [IP: {$ip}] [USER: {$user}] {$message}{$log_context}" . PHP_EOL;
    
    @file_put_contents(LOG_FILE_ERROR, $log_message, FILE_APPEND);
    
    // Also log to PHP error log
    error_log($message);
}

/**
 * Simpan debug info ke log file
 */
function log_debug($message, $context = [])
{
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        return;
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    
    $log_context = '';
    if (!empty($context)) {
        $log_context = ' | Context: ' . json_encode($context);
    }
    
    $log_message = "[{$timestamp}] [DEBUG] [IP: {$ip}] {$message}{$log_context}" . PHP_EOL;
    
    @file_put_contents(LOG_FILE_DEBUG, $log_message, FILE_APPEND);
}

/**
 * Log POST/FILES data untuk debugging
 */
function log_request_data()
{
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        return;
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    $log_message = "[{$timestamp}] {$method} {$uri}" . PHP_EOL;
    
    if ($_POST) {
        // Jangan log password atau token sensitif
        $safe_post = $_POST;
        unset($safe_post['password']);
        unset($safe_post['csrf_token']);
        $log_message .= "  POST: " . json_encode($safe_post) . PHP_EOL;
    }
    
    if ($_FILES) {
        $files_info = [];
        foreach ($_FILES as $key => $file) {
            $files_info[$key] = [
                'name' => $file['name'],
                'size' => $file['size'],
                'error' => $file['error']
            ];
        }
        $log_message .= "  FILES: " . json_encode($files_info) . PHP_EOL;
    }
    
    @file_put_contents(LOG_FILE_DEBUG, $log_message, FILE_APPEND);
}

/**
 * Clear log file
 */
function clear_log($log_file)
{
    if (file_exists($log_file)) {
        @file_put_contents($log_file, '');
        return true;
    }
    return false;
}

/**
 * Get log file contents dengan limit baris
 */
function get_log_contents($log_file, $limit = 100)
{
    if (!file_exists($log_file)) {
        return "Log file tidak ada: {$log_file}";
    }
    
    $lines = file($log_file);
    $total = count($lines);
    
    // Ambil last $limit lines
    $start = max(0, $total - $limit);
    $content = implode('', array_slice($lines, $start));
    
    return "Total lines: {$total} | Last {$limit} entries:\n" . $content;
}
?>
