<?php
require '../vendor/autoload.php';

session_start();
$routes = require_once __DIR__ . "/routes.php";
$route = $_GET['route'] ?? 'home';

if (!isset($_SESSION['user']) && !in_array($route, $routes['public'])) {
      $route = 'login';
}
  
if (isset($_SESSION['user']) && $route === 'login') {
      $route = 'home';
}

if (!in_array($route, $routes['allowed'])) {
      $route = '404';
}  

$controller = match($route) {
      "login", "login_submit", "signup", "signup_submit", "logout" => "AdasFinance\Controller\UserController",
      "home" => "AdasFinance\Controller\AssetController"
};

/** Convert snake case to cammel case */
$route = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $route))));

$route.="Action";
$object = new $controller();
$object->$route();

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/../view/header.html";
require_once __DIR__ . "/../view/footer.html";

?>


