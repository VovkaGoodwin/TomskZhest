<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 14:01
 */

namespace servicetech;


class Registry
{
  use TSingletone;

  protected static $properties = [];

  public function getProperty($name) {
    if (isset(self::$properties[$name])) {
      return self::$properties[$name];
    } else {
      return NULL;
    }
  }

  public function setProperty($name, $value) {
    self::$properties[$name] = $value;
  }

  public function getProperties() {
    return self::$properties;
  }

}