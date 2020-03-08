<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 13:32
 */

define('DEBUG', 1);
define('COOKIE_LIFETIME', 86400);
define('ROOT', dirname(__DIR__));
define('WWW', ROOT.'/public');
define('APP', ROOT.'/app');
define('CORE', ROOT.'/vendor/tomskzhest/core');
define('LIBS', CORE.'/libs');
define('SCRIPTS', CORE.'/scripts');
define('CACHE', ROOT.'/tmp/cache');
define('CONFIG', ROOT.'/config');
define('LAYOUT', 'default');

$app_path = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
$app_path = preg_replace('/[^\/]+$/', '', $app_path);
$app_path = str_replace('/public/', '', $app_path);
define('PATH', $app_path);
define('ADMIN', PATH.'/admin');

require_once ROOT.'/vendor/autoload.php';