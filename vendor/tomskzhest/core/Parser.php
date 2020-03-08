<?php

namespace servicetech;

class Parser
{

  const MIN_INTEGER = -2147483648;  // 4 bytes
  const MAX_INTEGER = 2147483647;   // 4 bytes

  public static function asStr($orig_str, $def = "") {
    if (!is_array($orig_str) && !is_object($orig_str) && !is_resource($orig_str)) {
      $orig_str = trim($orig_str);
      if (strlen($orig_str)) {
        return $orig_str;
      } else {
        return $def;
      }
    } else {
      return $def;
    }
  }

  public static function asInt($str, $def = 0) {
    $str = self::asStr($str, $def);
    $pattern = "/^((\-(\d+))|(\d+))$/";
    if (preg_match($pattern, $str)) {
      $val = intval($str);
      if ($val > self::MIN_INTEGER && $val < self::MAX_INTEGER) {
        return $val;
      } else {
        return $def;
      }
    } else {
      return $def;
    }
  }

  public static function getString($var, $max_length = NULL, $def = '') {
    return self::getStringFromArray($var, $max_length, $def, $_REQUEST);
  }

  public static function getStringFromArray($var, $max_length = NULL, $def = '', &$arr = NULL, $def_if_empty = FALSE) {
    if ($arr !== NULL && is_array($arr) && isset($arr[ $var ]) && is_string($arr[ $var ])) {
      $string = trim($arr[ $var ]);
      if ($def_if_empty && empty($string)) {
        $string = $def;
      }
    } else {
      $string = $def;
    }
    if (intval($max_length) && mb_strlen($string) > $max_length) {
      $string = mb_substr($string, 0, $max_length);
    }
    return $string;
  }

  public static function getInt($var, $def = 0, $def_if_empty = FALSE) {
    return self::getIntFromArray($var, $def, $_REQUEST, $def_if_empty);
  }

  public static function getIntFromArray($var, $def = 0, &$arr = NULL, $def_if_empty = FALSE) {
    if ($arr !== NULL && is_array($arr)) {
      if ($def_if_empty && empty($arr[ $var ])) {
        return $def;
      }
      if (isset($arr[ $var ]) && is_numeric($arr[ $var ])) {
        return self::asInt($arr[ $var ], $def);
      }
    }
    return $def;
  }

}
