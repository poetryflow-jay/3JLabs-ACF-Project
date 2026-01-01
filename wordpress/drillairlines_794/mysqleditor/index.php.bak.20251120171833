<?php
declare(strict_types=1);

/* --- Optional SSO redirect helper (top-level only) --- */
if (php_sapi_name() !== 'cli') {
    // Treat AJAX / PMA internal routes as non-top-level
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $isAjax = true;
    } elseif (isset($_GET['route'])) {
        $isAjax = true; // PMA uses index.php?route=... for background calls
    } else {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false || strpos($accept, 'text/javascript') !== false) {
            $isAjax = true;
        }
    }

    // Only redirect if it’s a top-level navigation (no route), not an AJAX call
    $token = $_GET['app-token'] ?? $_GET['token'] ?? null;

    if ($token !== null) {
        // Cap length
        $token = substr($token, 0, 256);

        // Allow only safe characters (same rule as in signon.php)
        $token = preg_replace('/[^A-Za-z0-9._-]/', '', $token);

        // If it became empty after sanitization, treat as missing
        if ($token === '') {
            $token = null;
        }
    }

    if ($token && !$isAjax) {
        header('Location: ./signon.php?app-token=' . urlencode($token));
        exit;
    }
}

use PhpMyAdmin\Common;
use PhpMyAdmin\Routing;

if (! defined('ROOT_PATH')) {
    // phpcs:disable PSR1.Files.SideEffects
    define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
    // phpcs:enable
}

if (PHP_VERSION_ID < 70205) {
    die('<p>PHP 7.2.5+ is required.</p><p>Currently installed version is: ' . PHP_VERSION . '</p>');
}

// phpcs:disable PSR1.Files.SideEffects
define('PHPMYADMIN', true);
// phpcs:enable

require_once ROOT_PATH . 'libraries/constants.php';

/**
 * Activate autoloader
 */
if (! @is_readable(AUTOLOAD_FILE)) {
    die(
        '<p>File <samp>' . AUTOLOAD_FILE . '</samp> missing or not readable.</p>'
        . '<p>Most likely you did not run Composer to '
        . '<a href="https://docs.phpmyadmin.net/en/latest/setup.html#installing-from-git">'
        . 'install library files</a>.</p>'
    );
}

require AUTOLOAD_FILE;

global $route, $containerBuilder, $request;

Common::run();

$dispatcher = Routing::getDispatcher();
Routing::callControllerForRoute($request, $route, $dispatcher, $containerBuilder);