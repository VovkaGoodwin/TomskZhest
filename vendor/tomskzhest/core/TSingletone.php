<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 14:02
 */

namespace servicetech;


trait TSingletone
{
  private static $instence;

  public static function instance(){
    if (empty(self::$instence)) {
      self::$instence = new self();
    }
    return self::$instence;
  }

}