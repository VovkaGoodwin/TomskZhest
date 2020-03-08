<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 13:53
 */

namespace tomskzhest;


class App
{

  public static $app;

  public function __construct() {
    $query = trim($_SERVER['QUERY_STRING'], '/');
    session_start([
      'cookie_lifetime' => COOKIE_LIFETIME,
    ]);
    self::$app = Registry::instance();
    $this->getParams();
    new ErrorHandler();
    Router::dispatch($query);
  }

  protected function getParams() {
    $params = require_once CONFIG.'/params.php';
    if (!empty($params)) {
      foreach ($params as $index => $param) {
        self::$app->setProperty($index, $param);
      }
    }
  }

}