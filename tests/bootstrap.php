<?php
declare(strict_types=1);

// Composer autoload (dev)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
}

// Basic safety: define ABSPATH for plugin files that gate on it.
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}


