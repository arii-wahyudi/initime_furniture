<?php
/**
 * Log Viewer - untuk melihat error dan debug logs
 * Path: /admin/log-viewer.php
 */

require __DIR__ . '/config.php';

// Only allow localhost
$allowed_ips = ['127.0.0.1', '::1', 'localhost'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    die('Access denied. Only localhost allowed.');
}

// Handle log clearing
if ($_POST['action'] == 'clear' && isset($_POST['log_type'])) {
    require_valid_csrf();
    $log_type = $_POST['log_type'];
    
    if ($log_type === 'error') {
        clear_log(LOG_FILE_ERROR);
        $message = 'Error log cleared';
    } elseif ($log_type === 'debug') {
        clear_log(LOG_FILE_DEBUG);
        $message = 'Debug log cleared';
    } elseif ($log_type === 'all') {
        clear_log(LOG_FILE_ERROR);
        clear_log(LOG_FILE_DEBUG);
        clear_log(__DIR__ . '/../../logs/php-error.log');
        $message = 'All logs cleared';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Viewer</title>
    <link href="<?php echo $BOOTSTRAP_CSS; ?>" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        .log-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .log-content {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 600px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .log-empty {
            color: #999;
            font-style: italic;
        }
        .error-line {
            color: #d32f2f;
        }
        .debug-line {
            color: #1976d2;
        }
        .info-line {
            color: #388e3c;
        }
        .btn-group-custom {
            display: flex;
            gap: 10px;
        }
        .info-badge {
            display: inline-block;
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1 class="mb-4">Log Viewer</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Error Log -->
        <div class="log-container">
            <div class="log-header">
                <div>
                    <h3>Error Log</h3>
                    <span class="info-badge">
                        <?php echo file_exists(LOG_FILE_ERROR) ? filesize(LOG_FILE_ERROR) . ' bytes' : '0 bytes'; ?>
                    </span>
                </div>
                <div class="btn-group-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearErrorModal">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </div>
            <div class="log-content">
                <?php 
                    if (file_exists(LOG_FILE_ERROR) && filesize(LOG_FILE_ERROR) > 0) {
                        echo htmlspecialchars(get_log_contents(LOG_FILE_ERROR, 200));
                    } else {
                        echo '<span class="log-empty">No errors logged yet</span>';
                    }
                ?>
            </div>
        </div>
        
        <!-- Debug Log -->
        <div class="log-container">
            <div class="log-header">
                <div>
                    <h3>Debug Log</h3>
                    <span class="info-badge">
                        <?php echo file_exists(LOG_FILE_DEBUG) ? filesize(LOG_FILE_DEBUG) . ' bytes' : '0 bytes'; ?>
                    </span>
                </div>
                <div class="btn-group-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearDebugModal">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </div>
            <div class="log-content">
                <?php 
                    if (file_exists(LOG_FILE_DEBUG) && filesize(LOG_FILE_DEBUG) > 0) {
                        echo htmlspecialchars(get_log_contents(LOG_FILE_DEBUG, 200));
                    } else {
                        echo '<span class="log-empty">No debug logs yet. Enable DEBUG_MODE in config.php</span>';
                    }
                ?>
            </div>
        </div>
        
        <!-- PHP Error Log -->
        <div class="log-container">
            <div class="log-header">
                <div>
                    <h3>PHP System Error Log</h3>
                    <span class="info-badge">
                        <?php 
                            $php_log = __DIR__ . '/../../logs/php-error.log';
                            echo file_exists($php_log) ? filesize($php_log) . ' bytes' : '0 bytes'; 
                        ?>
                    </span>
                </div>
                <div class="btn-group-custom">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearPhpModal">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </div>
            <div class="log-content">
                <?php 
                    $php_log = __DIR__ . '/../../logs/php-error.log';
                    if (file_exists($php_log) && filesize($php_log) > 0) {
                        echo htmlspecialchars(get_log_contents($php_log, 200));
                    } else {
                        echo '<span class="log-empty">No PHP errors logged</span>';
                    }
                ?>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="log-container" style="background-color: #f0f7ff; border-left: 4px solid #1976d2;">
            <h5>How to Use</h5>
            <ul>
                <li><strong>Error Log:</strong> Captures application errors with timestamp, IP, and user</li>
                <li><strong>Debug Log:</strong> Detailed debugging info when DEBUG_MODE is enabled</li>
                <li><strong>PHP System Log:</strong> Raw PHP errors and warnings</li>
                <li>Click <strong>Refresh</strong> to update the view</li>
                <li>Click <strong>Clear</strong> to clear specific logs</li>
            </ul>
        </div>
    </div>
    
    <!-- Clear Modals -->
    <div class="modal fade" id="clearErrorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear Error Log?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clear the error log? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="clear">
                        <input type="hidden" name="log_type" value="error">
                        <button type="submit" class="btn btn-danger">Clear Error Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="clearDebugModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear Debug Log?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clear the debug log? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="clear">
                        <input type="hidden" name="log_type" value="debug">
                        <button type="submit" class="btn btn-danger">Clear Debug Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="clearPhpModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear PHP Error Log?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clear the PHP error log? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="clear">
                        <input type="hidden" name="log_type" value="php">
                        <button type="submit" class="btn btn-danger">Clear PHP Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo $BOOTSTRAP_JS; ?>"></script>
</body>
</html>
