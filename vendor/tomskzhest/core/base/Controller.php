<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 12:13
 */

namespace servicetech\base;


use servicetech\Cache;

abstract class Controller
{
  protected $route;
  protected $controller;
  protected $model;
  protected $view;
  protected $prefix;
  protected $layout;
  protected $breadCrumbs = ['home' => '', 'switch' => '', 'port' => '', 'street' => ''];
  protected $data = [];
  protected $meta = ['title' => '', 'desc' => '', 'keywords' => ''];
  protected $cache;

  public function __construct($route) {
    $this->route = $route;
    $this->controller = $route['controller'];
    $this->view = $route['action'];
    $this->prefix = $route['prefix'];
    $this->model = $route['controller'];
    $this->cache = Cache::instance();
  }

  public function getView() {
    $viewObject = new View($this->route, $this->layout, $this->view, $this->meta, $this->breadCrumbs);
    $viewObject->render($this->data);
  }

  public function set($data) {
    $this->data = $data;
  }

  public function setBreadCrumbs($swith = '', $port = '', $home = '', $sreet =''){
    $this->breadCrumbs = ['home' => $home, 'switch' => $swith, 'port' => $port, 'street' => $sreet];
  }

  public function setMeta($title = '', $desc = '', $keyWords = '') {
    $this->meta = ['title' => $title, 'desc' => $desc, 'keywords' => $keyWords];
  }
  
}