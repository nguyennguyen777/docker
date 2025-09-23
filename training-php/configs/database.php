<?php
// Read database configuration from environment variables with sane defaults for Docker
define('DB_HOST', getenv('DB_HOST') ?: 'web-mysql');
define('DB_USER', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'root');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_DATABASE') ?: 'app_web1');
