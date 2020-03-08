<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 15.09.2019
 * Time: 10:31
 */

namespace tomskzhest;


use RedBeanPHP\R;

class Db
{
  use TSingletone;

  protected function __construct() {
    class_alias('\RedBeanPHP\R', '\R');
    $db_auth = require_once CONFIG.'/config_db.php';
    R::setup($db_auth['dsn'], $db_auth['user'], $db_auth['password']);
  }

}