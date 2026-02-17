<?php

/**
 * File handling utilities
 * Centralizes file upload, validation, and deletion logic
 */

/**
 * Validate and save uploaded file
 * @param array $file $_FILES array element
 * @param string $target_dir Target directory to save file
 * @param array $allowed_mimes Allowed MIME types
 * @param string $prefix Optional prefix for filename
 * @return string|false Filename on success, false on failure
 */
function handle_file_upload($file, $target_dir, $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'], $prefix = '')
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (!isset($file['type']) || !in_array($file['type'], $allowed_mimes)) {
        return false;
    }

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = ($prefix ? $prefix . '-' : '') . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = $target_dir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }

    return false;
}

/**
 * Save base64 encoded image (e.g., from AI preview)
 * @param string $base64_data Base64 image data with MIME type prefix
 * @param string $target_dir Target directory
 * @param string $suffix Optional suffix before extension
 * @return string|false Filename on success, false on failure
 */
function handle_base64_image($base64_data, $target_dir, $suffix = '-nobg')
{
    if (!preg_match('#^data:(image/\w+);base64,(.*)$#', $base64_data, $m)) {
        return false;
    }

    $mime = $m[1];
    $b64 = $m[2];
    $data = base64_decode($b64, true);

    if ($data === false) {
        return false;
    }

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext = 'png';
    if ($mime === 'image/jpeg') {
        $ext = 'jpg';
    } elseif ($mime === 'image/webp') {
        $ext = 'webp';
    }

    $filename = bin2hex(random_bytes(8)) . $suffix . '.' . $ext;
    $target = $target_dir . '/' . $filename;

    if (file_put_contents($target, $data) !== false) {
        return $filename;
    }

    return false;
}

/**
 * Delete file safely
 * @param string $file_path Full file path
 * @return bool Success
 */
function delete_file($file_path)
{
    if (file_exists($file_path) && is_file($file_path)) {
        return @unlink($file_path);
    }
    return false;
}

/**
 * Generate unique filename with random bytes
 * @param string $ext File extension (without dot)
 * @param string $prefix Optional prefix
 * @return string Generated filename
 */
function generate_filename($ext, $prefix = '')
{
    $filename = ($prefix ? $prefix . '-' : '') . bin2hex(random_bytes(8)) . '.' . $ext;
    return $filename;
}
