<?php
declare(strict_types=1);

/* ---------------- Session / cookies ---------------- */
session_set_cookie_params([
    'lifetime' => 1440,   // match SignonCookieParams lifetime
    'path'     => '/',
    'domain'   => '',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'None',
]);

session_name('kinsta_pma_sso'); // must match config.inc.php SignonSessionName
@session_start();

/* ---------------- Helper: detect AJAX-ish requests ---------------- */
function isAjaxLike(): bool {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false || strpos($accept, 'text/javascript') !== false) {
        return true;
    }
    if (isset($_GET['route'])) { // e.g. index.php?route=/ background calls
        return true;
    }
    return false;
}

/* ---------------- Helpers to short-circuit for XHR ---------------- */
function okAjaxNoop(): void {
    http_response_code(204); // no content, but success
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    exit;
}
function abortAjaxAuth(string $msg = 'SESSION_EXPIRED'): void {
    http_response_code(401);
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg;
    exit;
}

/* ---------------- Clean fallback login (final compact phpMyAdmin look) ---------------- */
function renderFallbackLogin(bool $fromLogout = false): void {
  header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>phpMyAdmin</title>
<link rel="icon" href="./favicon.ico">
<style>
:root{
  --card-bg:#ececec;
  --card-border:#999;
  --header-bg:#bcbcbc;
  --header-text:#fff;
  --header-inset:#888;
  --footer-bg:#c7cfd6;
  --input-border:#aaa;
}

html,body{height:100%;margin:0;padding:0}
body{
  font:13px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;
  background:#fff;
  display:flex;justify-content:center;align-items:flex-start;
  color:#222;
}
.wrap{
  width:380px;    /* smaller overall */
  margin-top:60px;
  text-align:center;
}
.logo img{height:40px;margin-bottom:8px;}
h1{font-size:17px;margin:0 0 16px;font-weight:700;}

/* Card */
.card{
  background:var(--card-bg);
  border:1px solid var(--card-border);
  border-radius:3px;
  box-shadow:2px 2px 4px #ccc;
  margin:14px 0;
  text-align:left;
}
.card-body{padding:12px 14px;}
.card-header{
  display:inline-block;position:relative;top:-11px;left:10px;
  background:var(--header-bg);
  color:var(--header-text);
  border:1px solid #aaa;
  border-radius:2px;
  font-weight:700;
  font-size:13px;
  padding:3px 8px;
  text-shadow:0 1px 0 #666;
  box-shadow:1px 1px 10px var(--header-inset) inset,2px 2px 10px #bbb;
}

label{display:block;font-size:13px;margin-bottom:4px;}
select,input[type=text],input[type=password]{
  width:100%;
  height:25px;
  padding:3px 6px;
  border:1px solid var(--input-border);
  border-radius:2px;
  box-sizing:border-box;
}
.form-row{margin-bottom:10px;}

/* Footer separated */
.card-footer{
  background:var(--footer-bg);
  border-top:1px solid #b0b6bc;
  padding:6px 10px;
  text-align:right;
  border-radius:0 0 3px 3px;
}
button{
  padding:4px 12px;
  border:1px solid #888;
  border-radius:12px;
  background:linear-gradient(#f8f9fa,#d5dbe1);
  box-shadow:0 1px 0 #fff inset,0 1px 2px rgba(0,0,0,.15);
  font-weight:700;
  font-size:13px;
  cursor:pointer;
}
button:hover{filter:brightness(0.97);}
.note{margin-top:10px;color:#666;font-size:12px;text-align:center;}
</style>
</head>
<body>
  <div class="wrap">
    <div class="logo"><img src="themes/pmahomme/img/kinsta_logo_right.svg" alt="phpMyAdmin"></div>
    <h1>Welcome to phpMyAdmin</h1>

    <div class="card">
      <div class="card-body">
        <div class="card-header">Language</div>
        <div class="form-row">
          <select><option selected>English</option></select>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="card-header">Log in <span style="display:inline-block;width:13px;height:13px;margin-left:5px;background:linear-gradient(#e8edf3,#c8d1d8);border:1px solid #9aa3ab;border-radius:50%;box-shadow:0 1px 0 #fff inset;"></span></div>
        <form action="signon.php" method="post" autocomplete="on">
          <div class="form-row">
            <label for="user">Username:</label>
            <input id="user" name="user" type="text" autocomplete="username" required>
          </div>
          <div class="form-row" style="margin-bottom:14px;">
            <label for="password">Password:</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
          </div>
          <div class="card-footer">
            <button type="submit">Log in</button>
          </div>
        </form>
      </div>
    </div>

    <div class="note">
      <?php echo $fromLogout ? 'You have been signed out.' : ''; ?>
    </div>
  </div>
</body>
</html>
<?php
  exit;
}


/* ---------------- Clear only SSO-related session keys ---------------- */
function clearSsoSession(): void {
    unset(
        $_SESSION['PMA_single_signon_user'],
        $_SESSION['PMA_single_signon_password'],
        $_SESSION['PMA_single_signon_host'],
        $_SESSION['PMA_single_signon_port'],
        $_SESSION['PMA_single_signon_socket'],
        $_SESSION['PMA_single_signon_HMAC_secret'],
        $_SESSION['PMA_single_signon_cfgupdate'],
        $_SESSION['PMA_SSO_used_tokens'],
        $_SESSION['PMA_single_signon_error_message']
    );
}


/* === If PMA bounced back with an auth error, clear SSO and show the form === */
$cameBackWithError = isset($_GET['error']) || !empty($_SESSION['PMA_single_signon_error_message']);
if ($cameBackWithError) {
    clearSsoSession();
    renderFallbackLogin(false);
}

/* ---------------- Fallback manual login ---------------- */
if (isset($_POST['user'])) {
    $_SESSION['PMA_single_signon_user']     = (string)$_POST['user'];
    $_SESSION['PMA_single_signon_password'] = (string)$_POST['password'];
    $_SESSION['PMA_single_signon_host']     = (string)($_POST['host'] ?? '');
    $_SESSION['PMA_single_signon_port']     = (string)($_POST['port'] ?? '');
    if (empty($_SESSION['PMA_single_signon_HMAC_secret'])) {
        $_SESSION['PMA_single_signon_HMAC_secret'] = hash('sha1', bin2hex(random_bytes(16)));
    }
    $_SESSION['PMA_single_signon_cfgupdate'] = ['verbose' => 'localhost'];
    @session_write_close();
    header('Location: ./index.php');
    exit;
}

/* ---------------- Logout: show clean form ---------------- */
if (isset($_GET['loggedout'])) {
    renderFallbackLogin(true);
}

/* ---------------- Inputs ---------------- */
$token = $_GET['app-token'] ?? $_GET['token'] ?? null;

if ($token !== null) {
    // cap length
    $token = substr($token, 0, 256);

    // allow only safe characters
    $token = preg_replace('/[^A-Za-z0-9._-]/', '', $token);

    // if token becomes empty, treat it as missing
    if ($token === '') {
        $token = null;
    }
}

$haveSSO = !empty($_SESSION['PMA_single_signon_user']) && !empty($_SESSION['PMA_single_signon_password']);
if (!$token) {
    if (isAjaxLike()) {
        $haveSSO ? okAjaxNoop() : abortAjaxAuth();
    } else {
        if ($haveSSO) {
            @session_write_close();
            header('Location: ./index.php');
            exit;
        }
        renderFallbackLogin(false);
    }
}

/* If SSO already established but a stray token shows up (e.g. copied URL), do not re-validate */
if ($haveSSO) {
    if (isAjaxLike()) { okAjaxNoop(); }
    header('Location: ./index.php'); exit;
}

/* ---------------- Helpers ---------------- */
function parseDbHost(string $raw): array {
    $host='localhost'; $port='3306'; $socket=null;
    $s = trim($raw);
    if ($s === '') return [$host,$port,$socket];
    if ($s[0] === '/') return [$host,$port,$s];
    if (strpos($s, ':') !== false) {
        [$h,$rest] = explode(':',$s,2); $host=$h;
        if ($rest !== '' && $rest[0] === '/') $socket=$rest;
        elseif (ctype_digit($rest)) $port=$rest;
        return [$host,$port,$socket];
    }
    return [$s,$port,$socket];
}

/** Only use the localhost Nginx+Lua endpoint for env id */
function getEnvIdViaLocalNginx(?array &$dbg = null): ?string {
    if (!function_exists('curl_init')) {
        return null;
    }

    $tries = [
        ['url' => 'https://localhost/kinsta/api/v1/details', 'headers' => [],                   'insecure' => true],
        ['url' => 'http://127.0.0.1/kinsta/api/v1/details',  'headers' => ['Host: localhost'], 'insecure' => false],
    ];

    foreach ($tries as $t) {
        $ch = curl_init($t['url']);
        $opts = [
            CURLOPT_HTTPHEADER     => $t['headers'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT        => 3,
        ];
        if ($t['insecure']) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (is_array($dbg)) {
            $dbg[] = [
                'url'  => $t['url'],
                'code' => $code,
                'err'  => $err,
                'body' => is_string($resp) ? substr($resp, 0, 200) : null,
            ];
        }

        if ($resp !== false && $code === 200) {
            $j = json_decode($resp, true);

            if (
                is_array($j) &&
                isset($j['meta_kinsta_env_id']) &&
                is_scalar($j['meta_kinsta_env_id'])
            ) {
                $envId = trim((string) $j['meta_kinsta_env_id']);
                if ($envId !== '') {
                    return $envId;
                }
            }
        }
    }

    return null;
}


/* ---------------- Derive env id ---------------- */
$dbgLua = [];
$envId  = getEnvIdViaLocalNginx($dbgLua);
if (!$envId) { isAjaxLike() ? abortAjaxAuth() : renderFallbackLogin(false); }

/* ---------------- Prevent double-validation ---------------- */
if (!isset($_SESSION['PMA_SSO_used_tokens'])) $_SESSION['PMA_SSO_used_tokens'] = [];
if (in_array($token, $_SESSION['PMA_SSO_used_tokens'], true)) {
    // Already validated in this session; don’t try again
    if (isAjaxLike()) { okAjaxNoop(); }
    @session_write_close();
    header('Location: ./index.php');
    exit;
}

/* ---------------- Validate token ---------------- */
if (!function_exists('curl_init')) { isAjaxLike() ? abortAjaxAuth() : renderFallbackLogin(false); }

$GQL_ENDPOINT = getenv('KINSTA_GQL_ENDPOINT') ?: 'https://graphql-router.kinsta.com';

$gql = <<<'GRAPHQL'
mutation ValidateAppKey($idEnvironment: String!, $token: String!) {
  validateAppKey(idEnvironment: $idEnvironment, token: $token) {
    id
    createdAt
    status
  }
}
GRAPHQL;

$payload = json_encode(['query'=>$gql,'variables'=>['idEnvironment'=>$envId,'token'=>$token]], JSON_UNESCAPED_SLASHES);
$ch = curl_init($GQL_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 8,
]);
$response = curl_exec($ch);
$curlErr  = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) { isAjaxLike() ? abortAjaxAuth('NETWORK_ERROR') : renderFallbackLogin(false); }

$data  = json_decode($response, true);
$valid = isset($data['data']['validateAppKey']) && is_array($data['data']['validateAppKey']);
if (!$valid) { isAjaxLike() ? abortAjaxAuth('TOKEN_INVALID') : renderFallbackLogin(false); }

$_SESSION['PMA_SSO_used_tokens'][] = $token;

/* ---------------- Load DB creds from secret ---------------- */
if (
    !defined('SERVER_SECRET_DB_USER') ||
    !defined('SERVER_SECRET_DB_PASSWORD') ||
    !defined('SERVER_SECRET_DB_HOST')
) {
    // If the constants are not available, we can't SSO; fall back to login form / 401.
    isAjaxLike() ? abortAjaxAuth('NO_DB_CONSTANTS') : renderFallbackLogin(false);
}

[$dbHost, $dbPort, $dbSocket] = parseDbHost(SERVER_SECRET_DB_HOST);

/* ---------------- Set PMA SSO session vars ---------------- */
$_SESSION['PMA_single_signon_user']     = SERVER_SECRET_DB_USER;
$_SESSION['PMA_single_signon_password'] = SERVER_SECRET_DB_PASSWORD;

if ($dbSocket) {
    $_SESSION['PMA_single_signon_socket'] = $dbSocket;
    $_SESSION['PMA_single_signon_host']   = 'localhost';
    $_SESSION['PMA_single_signon_port']   = '';
} else {
    $_SESSION['PMA_single_signon_host']   = $dbHost;
    $_SESSION['PMA_single_signon_port']   = $dbPort;
}

if (empty($_SESSION['PMA_single_signon_HMAC_secret'])) {
    $_SESSION['PMA_single_signon_HMAC_secret'] = hash('sha1', bin2hex(random_bytes(16)));
}
$_SESSION['PMA_single_signon_cfgupdate'] = ['verbose' => 'localhost'];

@session_write_close();

/* ---------------- Hand off to PMA ---------------- */
header('Location: ./index.php');
exit;