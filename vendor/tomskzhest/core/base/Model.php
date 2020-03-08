<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 18:05
 */

namespace tomskzhest\base;


use tomskzhest\Db;

abstract class Model
{

  protected $attributes = [];
  protected $errors = [];
  protected $rules = [];

  public function __construct() {
    Db::instance();
  }

}