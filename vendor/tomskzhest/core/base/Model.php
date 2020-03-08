<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 18:05
 */

namespace servicetech\base;


use servicetech\Db;

abstract class Model
{

  protected $attributes = [];
  protected $errors = [];
  protected $rules = [];

  public function __construct() {
    Db::instance();
  }

}