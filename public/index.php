<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 12:58
 */

use tomskzhest\App;
use tomskzhest\Cache;

require_once dirname(__DIR__)."/config/init.php";
require_once LIBS.'/functions.php';
require_once CONFIG.'/roots.php';

if(empty($_COOKIE['lh']) && !preg_match('/login/', $_SERVER['QUERY_STRING']) ){
  header("Location: " . PATH . "/login");
} elseif (!empty($_COOKIE['lh'])) {
  $login = explode('-', base64_decode($_COOKIE['lh']))[0];
  $cache = Cache::instance()->get($login);
  if (empty($cache) || $cache != $_COOKIE['lh'] ) {
    setcookie('lh', '', 0, '/');
    header("Location: " . PATH);
  }
}


new App();