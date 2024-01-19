<?php
require '../vendor/autoload.php';

session_start();
$allowed_routes = require_once __DIR__ . "/routes.php";
$route = $_GET['route'] ?? 'home';

if (!isset($_SESSION['user']) && $route !== 'login') {
      $route = 'signin';
}
  
if (isset($_SESSION['user']) && $route === 'signup') {
      $route = 'home';
}

if (!in_array($route, $allowed_routes)) {
      $route = '404';
}  

$controller = match($route) {
      "404", "login", "signin", "signup", "logout" => "AdasFinance\Controller\UserController",
      "home" => "AdasFinance\Controller\AssetController"
};

$route.="Action";
$object = new $controller();
$object->$route();

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/../view/header.html";
require_once __DIR__ . "/../view/footer.html";

?>


