<?php

/**
 * PHPUnit bootstrap file.
 *
 * Sets up the testing environment before tests run.
 */

// Force testing environment variables BEFORE anything else loads
putenv('DB_DATABASE=ironman_ai_testing');
$_ENV['DB_DATABASE'] = 'ironman_ai_testing';
$_SERVER['DB_DATABASE'] = 'ironman_ai_testing';

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';
