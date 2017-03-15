<?php

// data AFTER the 1st ? in URL
//echo 'Requested URL = '. '"'.$_SERVER['QUERY_STRING'].'"<br><br>';

//require '../App/Controllers/Home.php';
//require '../Core/Router.php';

/*
$router = new Core\Router();

// Add routes (argument 1: route, argument 2: parameters (controller & action))
$router->add('',          ['controller' => 'Home',  'action' => 'index']);
$router->add('posts',     ['controller' => 'Posts', 'action' => 'index']);
//$router->add('posts/new', ['controller' => 'Posts', 'action' => 'new']);
$router->add('{controller}/{action}');
$router->add('admin/{action}/{controller}');
$router->add('{controller}/{id:\d+}/{action}');

echo '<pre>';
print_r($router->getRoutes());
echo '</pre>';

// match the requested route
$url = $_SERVER['QUERY_STRING'];

if($router->match($url))
{
  echo 'Match found: <br>';
  echo 'Params: <br>';
  echo '<pre>';
  print_r($router->getParams());
  echo '</pre>';
}
else
{
  echo "No route found for URL '$url'";
  // echo 'Params: <br>';
  // echo '<pre>';
  // print_r($router->getParams());
  // echo '</pre>';
}

exit();
*/

/**
 * Front controller
 *
 * PHP  version 7.0
 */

/**
 * Composer
 */
// loads class files; eliminates need to require files to use the class
require '../vendor/autoload.php';

// must come AFTER autoloader for classes to be known to SESSION variable
session_start();

/**
 * Twig
 */
Twig_Autoloader::register();


/**
 * Error and Exception handling
 */
error_reporting(E_ALL); // — Sets which PHP errors are reported
// set_error_handler — Sets a user-defined error handler function
// call static errorHandler() method in Core/Error class
set_error_handler('Core\Error::errorHandler');
// set_exception_handler — Sets a user-defined exception handler function
// call static exceptionHandler() method in Core/Error class
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add routes (argument 1: route, argument 2: parameters (controller & action))
$router->add('', ['controller' => 'Home',  'action' => 'index']);
$router->add('about', ['controller' => 'About',  'action' => 'index']);
$router->add('calendar', ['controller' => 'Calendar',  'action' => 'index']);
$router->add('services', ['controller' => 'Services', 'action' => 'index']);
$router->add('contact', ['controller' => 'Contact', 'action' => 'index']);
$router->add('testimonials', ['controller' => 'Testimonials', 'action' => 'index']);
$router->add('register', ['controller' => 'Register', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'index']);
$router->add('logout', ['controller' => 'Logout', 'action' => 'index']);
$router->add('terms', ['controller' => 'Terms', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']); // assign the namespace
$router->add('{controller}/{id:\d+}/{action}'); // 'id' can be anything
$router->add('{controller}/{action}/{id:\d+}'); // controller, action and id can be in any order

// call dispatch method of Router class
$router->dispatch($_SERVER['QUERY_STRING']);
