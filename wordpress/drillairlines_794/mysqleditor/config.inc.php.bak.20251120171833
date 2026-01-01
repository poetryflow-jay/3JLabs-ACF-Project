<?php
/*
 * Kinsta phpMyAdmin configuration file
 * SSO Type Authentication
 * Date : Wed 12th November 2025
 */

/* Servers configuration */
$i = 0; $i++;

/* Server */
$cfg['Servers'][$i]['host']          = 'localhost';
$cfg['Servers'][$i]['connect_type']  = 'socket';
$cfg['Servers'][$i]['auth_type']     = 'signon';
$cfg['Servers'][$i]['SignonSession'] = 'kinsta_pma_sso';
$cfg['Servers'][$i]['SignonURL']     = './signon.php';
$cfg['Servers'][$i]['LogoutURL']     = './logout.php';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
$cfg['Servers'][$i]['SignonCookieParams'] = [
    'lifetime' => 1440,
    'path'     => '/',
    'domain'   => '',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Strict',
];

/* End of servers configuration */

$cfg['DefaultLang'] = 'en';
$cfg['ServerDefault'] = 1;
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['UserprefsDisallow'] = array('VersionCheck', 'SendErrorReports');
$cfg['ExecTimeLimit'] = 0;
$cfg['VersionCheck'] = false;
$cfg['SendErrorReports'] = 'never';
$cfg['TitleDefault'] = '@HTTP_HOST@ | Kinsta MySQL Editor';
$cfg['TitleTable'] = '@HTTP_HOST@ / @VSERVER@ / @DATABASE@ / @TABLE@ | Kinsta MySQL Editor';
$cfg['TitleDatabase'] = '@HTTP_HOST@ / @VSERVER@ / @DATABASE@ | Kinsta MySQL Editor';
$cfg['TitleServer'] = '@HTTP_HOST@ / @VSERVER@ | Kinsta MySQL Editor';
$cfg['blowfish_secret'] = 'FT8R|c-u==8WWMv[V=()i6j0agqj$_y9uL)nU';
$cfg['Servers'][$i]['hide_db'] = 'information_schema';
$cfg['PmaNoRelation_DisableWarning'] = true;