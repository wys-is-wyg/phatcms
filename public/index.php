<?php

/**
 * PhatCMS - Lightweight OOP MVC CMS
 * 
 * This is the front controller that handles all requests
 */

// Define the application root directory
define('APP_ROOT', dirname(__DIR__));

// Load the bootstrap file
require_once APP_ROOT . '/bootstrap/app.php';

// Initialize the application
$app = new \App\Core\Application();

// Run the application
$app->run(); 