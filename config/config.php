<?php

/**
 * PhatCMS - Main Configuration File
 */

// Environment (development, production)
define('ENV', $_ENV['APP_ENV'] ?? 'development');

// Application settings
define('APP_NAME', $_ENV['APP_NAME'] ?? 'PhatCMS');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// Database settings
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'phatcms');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Path definitions
define('VIEWS_PATH', APP_ROOT . '/resources/views');
define('STORAGE_PATH', APP_ROOT . '/storage');
define('CACHE_PATH', STORAGE_PATH . '/cache');
define('LOG_PATH', APP_ROOT . '/log');

// Security
define('APP_KEY', $_ENV['APP_KEY'] ?? 'change-this-to-a-random-string'); 