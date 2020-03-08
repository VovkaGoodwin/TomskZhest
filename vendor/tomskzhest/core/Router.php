<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 10:55
 */

namespace tomskzhest;

class Router
{
  protected static $routes = [];
  protected static $route = [];

  public static function add($regexp, $route = []) {
    self::$routes[ $regexp ] = $route;
  }

  public static function getRoutes() {
    return self::$routes;
  }

  public static function getRoute() {
    return self::$route;
  }

  public static function dispatch($url) {
    $url = self::removeQueryString($url);
    if (self::matchRoute($url)) {
      $controller = 'app\controllers\\' . self::$route[ 'prefix' ] . self::$route[ 'controller' ] . 'Controller';
      if (class_exists($controller)) {
        $controllerObject = new $controller(self::$route);
        $action = self::lowerCamelCase(self::$route[ 'action' ] . 'Action');
        if (method_exists($controllerObject, $action)) {
          $controllerObject->$action();
          $controllerObject->getView();
        } else {
          throw new \Exception("Метод {$controller}::<b>{$action}</b> не найден", 404);
        }
      } else {
        throw new \Exception("Конроллер {$controller} не найден", 404);
      }
    } else {
      throw new \Exception('Ошибка', 404);
    }
  }

  public static function matchRoute($url) {
    foreach (self::$routes as $pattern => $route) {
      if (preg_match("/{$pattern}/", $url, $matches)) {
        foreach ($matches as $key => $value) {
          if (is_string($key)) $route[ $key ] = $value;
        }
        $route[ 'action' ] = (!empty($route[ 'action' ])) ? $route[ 'action' ] : 'index';
        $route[ 'prefix' ] = (isset($route[ 'prefix' ])) ? $route[ 'prefix' ] . '\\' : '';
        $route[ 'controller' ] = self::upperCamelCase($route[ 'controller' ]);
        self::$route = $route;
        return TRUE;
      }
    }
    return FALSE;
  }

  protected static function upperCamelCase($name) {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
  }

  protected static function lowerCamelCase($name) {
    return lcfirst(self::upperCamelCase($name));
  }

  protected static function removeQueryString($string) {
    if ($string) {
      $params = explode('&', $string, 2);
      if (FALSE === strpos($params[ 0 ], '=') && !empty($params)) {
        return trim($params[ 0 ], '/');
      } else {
        return '';
      }
    }
  }

}