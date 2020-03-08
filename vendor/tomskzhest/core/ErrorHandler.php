<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 07.09.2019
 * Time: 21:43
 */

namespace servicetech;


class ErrorHandler
{
  public function __construct() {
    if (DEBUG) {
      error_reporting(-1);
    } else {
      error_reporting(0);
    }
    set_exception_handler([ $this, 'exceptionHandler' ]);
  }

  public function exceptionHandler($e) {
    $this->logErrors($e->getMessage(), $e->getFile(), $e->getLine());
    $this->displayError('Исключение', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
  }

  protected function logErrors ($message = '', $file='', $line ='') {
    $errorMes = '['.date('Y-m-d H:i:s')."] Текст ошибки: {$message} | Файл: {$file} | Строка: {$line} \n";
    error_log($errorMes, 3, ROOT.'/tmp/error.log');
  }

  protected function displayError ($errorNo, $errorStr, $errorFile, $errorLine, $response = 404) {
    http_response_code($response);
    if ($response == 404 && !DEBUG) {
        require_once WWW.'/errors/404.php';
        die;
    } elseif ($response == 505) {
      require_once WWW.'/errors/505.php';
    } elseif (DEBUG) {
      require_once WWW.'/errors/dev.php';
    } elseif (!DEBUG) {
      require_once WWW.'/errors/prod.php';
    }
  }

  static public function log($message, $level = 0){
    $type = ($level) ? 'INFO' : 'ERROR';
    $errorMessage = '[LOG '.$type.']: '.$message.PHP_EOL;
    error_log($errorMessage, 3, ROOT.'/tmp/error.log');
  }

}