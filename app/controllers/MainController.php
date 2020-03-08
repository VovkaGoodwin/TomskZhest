<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 12:06
 */

namespace app\controllers;

use tomskzhest\Cache;

class MainController extends AppController
{

  public function indexAction(){
    $this->setMeta('tomskzhest');
  }
}