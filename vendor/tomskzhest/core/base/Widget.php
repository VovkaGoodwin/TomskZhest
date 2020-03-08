<?php


namespace tomskzhest\base;


abstract class Widget
{

  protected $tpl;
  protected $allProperties;
  protected $currentProperty;

  public function getAllProperties() {
    return null;
  }

  public function getCurrentProperty() {
    return null;
  }


  protected function run() {
    $this->allProperties = $this->getAllProperties();
    $this->currentProperty = $this->getCurrentProperty();
    echo $this->getHtml();
  }

  protected function getHtml() {
    ob_start();
    require_once $this->tpl;
    return ob_get_clean();
  }
}