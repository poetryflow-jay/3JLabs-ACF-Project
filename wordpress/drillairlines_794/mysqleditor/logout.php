<?php
declare(strict_types=1);

session_name('kinsta_pma_sso'); // must match config.inc.php
@session_start();

/* Wipe PHP session */
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 1440, $p['path'] ?? '/', $p['domain'] ?? '', $p['secure'] ?? false, $p['httponly'] ?? true);
}
@session_destroy();

/* Also expire common phpMyAdmin cookies so it can't auto-resume */
if (!headers_sent()) {
    $cookieParams = function_exists('session_get_cookie_params') ? session_get_cookie_params() : [];
    $path   = $cookieParams['path']   ?? '/';
    $domain = $cookieParams['domain'] ?? '';
    $secure = $cookieParams['secure'] ?? isset($_SERVER['HTTPS']);
    $httpOnly = $cookieParams['httponly'] ?? true;

    foreach ($_COOKIE as $name => $val) {
        $lname = strtolower($name);
        if (strpos($lname, 'pma') === 0 || strpos($lname, 'phpmyadmin') === 0) {
            setcookie($name, '', time() - 1440, $path, $domain, $secure, $httpOnly);
        }
    }
}

/* Go to our fallback login */
header('Location: ./signon.php?loggedout=1');
exit;
