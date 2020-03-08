<?php
/**
 * Created by Vovka_Goodwin.
 * Date: 14.09.2019
 * Time: 10:55
 */

use servicetech\Router;

Router::add('^admin$', [ 'controller' =>  'Main', 'action' => 'index', 'prefix' => 'admin' ]);
Router::add('^admin\/?(?P<controller>[a-z-]+)\/?(?P<action>[a-z-]+)?$', [ 'prefix' =>  'admin' ]);


Router::add('^$', [ 'controller' =>  'Main', 'action' => 'index' ]);
Router::add('^(?P<controller>[a-z-]+)\/?(?P<action>[a-z-]+)?$');
