<?php
// Set base path
define('BASE_PATH', __DIR__);

// Load all configurations
require BASE_PATH . '/functions/db_config.php';
require BASE_PATH . '/functions/permissions.php';
require BASE_PATH . '/functions/auth.php';

// Application settings
define('APP_NAME', 'Klinik Gigi');
define('SESSION_TIMEOUT', 1800); // 30 minutes
?>