<?php
// Admin config: session, CSRF token, DB connection and helpers.
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security settings
@ini_set('session.use_strict_mode', '1');
@ini_set('session.cookie_httponly', '1');
$secureCookie = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
if ($secureCookie) @ini_set('session.cookie_secure', '1');

if (!headers_sent()) {
    setcookie('csrf_token', $_SESSION['csrf_token'], [
        'expires' => 0,
        'path' => '/',
        'secure' => $secureCookie,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');

// Database connection
$db_host = 'localhost';
// $db_user = 'root';
// $db_pass = '';
$db_user = 'desadroi_fahmi';
$db_pass = 'KtHYm2dJL@yMaKx';
$db_name = 'desadroi_initime_x8y2';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}

// Fallback credentials
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = 'admin123';

// Load utility functions
require __DIR__ . '/utilities/database.php';
require __DIR__ . '/utilities/file-handler.php';
require __DIR__ . '/utilities/validation.php';
require __DIR__ . '/utilities/response.php';

function check_csrf($token)
{
    // Primary: session-backed token match (original behavior)
    if (!empty($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }

    // Secondary: double-submit cookie (some clients/frameworks send token from cookie)
    if (!empty($token) && !empty($_COOKIE['csrf_token']) && hash_equals($_COOKIE['csrf_token'], $token)) {
        return true;
    }

    // Tertiary fallback: same-origin check using Origin or Referer headers.
    // This is simpler and works well for browser form posts on the same site.
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
    if ($host) {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
        if (!empty($origin)) {
            $o = parse_url($origin);
            if ($o && isset($o['host'])) {
                $serverHost = preg_replace('/:\\d+$/', '', $host);
                if (strcasecmp($o['host'], $serverHost) === 0) {
                    return true;
                }
            }
        }
    }

    return false;
}

function require_admin()
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

// Upload settings
$UPLOADS_DIR = __DIR__ . '/../uploads';
if (!is_dir($UPLOADS_DIR)) mkdir($UPLOADS_DIR, 0755, true);
$PRODUCTS_UPLOAD_DIR = $UPLOADS_DIR . '/products';
if (!is_dir($PRODUCTS_UPLOAD_DIR)) mkdir($PRODUCTS_UPLOAD_DIR, 0755, true);

// Optional: remove.bg API key for background removal (leave empty to skip)
// Sign up at https://www.remove.bg if you want automatic background removal
$REMOVE_BG_API_KEY = 'R65zw4G86LdXhiZhnDeKLaww';

function slugify($text)
{
    $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

function public_image_url($img, $subdir = 'products')
{
    $img = trim((string)$img);
    if ($img === '') return 'assets/img/prod1.jpg';
    if (preg_match('#^https?://#i', $img)) return $img;
    // Normalize Windows backslashes and remove any leading ../ or slashes
    $img = str_replace('\\', '/', $img);
    $img = preg_replace('#^\.{1,2}/+#', '', $img);
    $img = ltrim($img, '/');

    // Determine application base path so URLs work both from admin/ and public pages.
    $scriptDir = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
    $appBase = '';
    if ($scriptDir) {
        // If current script is inside an admin folder, use parent dir as app base
        if (preg_match('#/admin$#', $scriptDir)) {
            $appBase = dirname($scriptDir);
        } else {
            $appBase = $scriptDir;
        }
        if ($appBase === '/' || $appBase === '\\' || $appBase === '.') $appBase = '';
    }

    // Build app base (may be empty). Return a root-relative URL so links work
    // whether opened from admin pages, public pages, or a new tab.
    $prefix = $appBase ? rtrim($appBase, '/') : '';

    // If value already contains a path (assets/... or uploads/... or folder), prefix with app base
    if (strpos($img, '/') !== false) {
        // Check if img already starts with app base path or absolute path - if so, don't duplicate it
        if ($prefix && strpos('/' . $img, '/' . trim($prefix, '/') . '/') === 0) {
            // Already has app base, use as-is
            return '/' . ltrim($img, '/');
        }
        // Also check if it's an absolute path from docroot
        if ($prefix && strpos($img, trim($prefix, '/') . '/') === 0) {
            return '/' . ltrim($img, '/');
        }
        $path = ($prefix !== '' ? $prefix . '/' : '') . ltrim($img, '/');
        return '/' . ltrim($path, '/');
    }

    // Otherwise assume it's a bare filename stored in uploads/<subdir>
    $path = ($prefix !== '' ? $prefix . '/' : '') . 'uploads/' . trim($subdir, '/') . '/' . $img;
    return '/' . ltrim($path, '/');
}

/**
 * Resolve image info: returns array with public URL, resolved filesystem path (if local) and exists flag.
 * Useful for admin diagnostics when images are uploaded via cPanel or other tools.
 */
function resolve_image_info($img, $subdir = 'products')
{
    $url = public_image_url($img, $subdir);
    $info = ['url' => $url, 'fs' => null, 'exists' => false];
    // if absolute http(s) URL, we cannot check local filesystem reliably
    if (preg_match('#^https?://#i', $url)) return $info;

    // Use DOCUMENT_ROOT as base if available, otherwise fallback to __DIR__/...
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if (!$docRoot) {
        // Fallback: compute from __DIR__ (which is admin/)
        // Project root = __DIR__/..
        $docRoot = dirname(__DIR__);
    }
    $docRoot = rtrim($docRoot, '/\\') . DIRECTORY_SEPARATOR;

    // Convert URL path to filesystem path: remove leading slash and convert path separators
    $urlPath = ltrim($url, '/');
    $urlPath = str_replace('/', DIRECTORY_SEPARATOR, $urlPath);
    $fsPath = $docRoot . $urlPath;

    // Try to resolve the real path
    $fs = realpath($fsPath);
    if ($fs && file_exists($fs)) {
        $info['fs'] = $fs;
        $info['exists'] = true;
        return $info;
    }

    // fallback: try uploads path directly with just the filename
    if ($img) {
        $alt = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . basename($img));
        if ($alt && file_exists($alt)) {
            $info['fs'] = $alt;
            $info['exists'] = true;
            return $info;
        }
    }

    // try project root (some users upload files directly to the project folder)
    if ($img) {
        $rootCandidate = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . basename($img));
        if ($rootCandidate && file_exists($rootCandidate)) {
            $info['fs'] = $rootCandidate;
            $info['exists'] = true;
            return $info;
        }
    }

    // final fallback: return computed path (even if it doesn't exist) and non-existence flag
    $info['fs'] = $fsPath;
    return $info;
}
