<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 12:34
 */

namespace tomskzhest\base;


class View
{
  protected $route;
  protected $controller;
  protected $model;
  protected $view;
  protected $prefix;
  protected $layout;
  protected $data = [];
  protected $breadCrumbs = [];
  protected $meta = ['title' => '', 'desc' => '', 'keywords' => ''];

  public $currentUser = '';

  public function __construct($route, $layout = '', $view = '', $meta, $breadCrumbs) {
    $this->route = $route;
    $this->controller = $route['controller'];
    $this->view = $view;
    $this->prefix = $route['prefix'];
    $this->model = $route['controller'];
    $this->meta = $meta;
    $this->breadCrumbs = $breadCrumbs;
    if($layout === FALSE) {
      $this->layout = FALSE;
    } else {
      $this->layout = (!empty($layout)) ? $layout : LAYOUT;
    }
    $this->prefix = str_replace('\\', '/', $this->prefix);
  }

  public function render($data) {
    $viewFile = APP."/views/{$this->prefix}{$this->controller}/{$this->view}.php";
    if (is_file($viewFile)) {
      ob_start();
      require_once $viewFile;
      $content = ob_get_clean();
    } else {
      throw new \Exception("Не найден вид {$viewFile} ", 404);
    }
    if (FALSE !== $this->layout) {
      $layoutFile = APP . "/views/loyauts/{$this->layout}.php";
      if (is_file($layoutFile)) {
        require_once $layoutFile;
        $meta = $this->meta;
      } else {
        throw new \Exception("Не найден шаблон {$layoutFile} ", 404);
      }
    }
  }

  public function getMeta() {
    $meta = "<title>{$this->meta['title']}</title>".PHP_EOL;
    $meta .= "<meta name='description' content='{$this->meta['desc']}'>".PHP_EOL;
    $meta .= "<meta name='keywords' content='{$this->meta['keywords']}'>".PHP_EOL;
    return $meta;
  }

  public function getBreadCrumbs(){
    $breadCrumbs = '';
    if (!empty($this->breadCrumbs['home']) && !empty($this->breadCrumbs['street'])) {
      $breadCrumbs .= "<li class='nav-item'>
                          <a class='nav-link' 
                             href='/tech/find/home?street={$this->breadCrumbs['street']}&build={$this->breadCrumbs['home']}'>
                             {$this->breadCrumbs['street']} - {$this->breadCrumbs['home']}
                          </a>
                        </li>";
    }
    if (!empty($this->breadCrumbs['switch'])) {
      $breadCrumbs .= "<li class='nav-item'>
                          <a class='nav-link' 
                             href='/tech/find/switch?switch={$this->breadCrumbs['switch']}'> {$this->breadCrumbs['switch']}
                          </a>
                        </li>";
    }
    if (!empty($this->breadCrumbs['switch']) && !empty($this->breadCrumbs['port'])) {
      $breadCrumbs .= "<li class='nav-item'>
                          <a class='nav-link' 
                             href='/tech/find/switch?switch={$this->breadCrumbs['switch']}&port={$this->breadCrumbs['port']}'>
                              {$this->breadCrumbs['port']}
                          </a>
                        </li>";
    }
    return $breadCrumbs;
  }

}