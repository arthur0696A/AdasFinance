<?php
require __DIR__ . "/../vendor/autoload.php";

use app\Controller\UserController;

session_start();
$allowed_routes = require_once __DIR__ . "/routes.php";
$route = $_GET['route'] ?? 'home';

if (!isset($_SESSION['user']) && $route !== 'login_submit') {
      $route = 'login';
}
  
if (isset($_SESSION['user']) && $route === 'login') {
      $route = 'home';
}

if (!in_array($route, $allowed_routes)) {
      $route = '404';
}  

$controller = match($route) {
      "404", "login", "login_submit", "logout" => "UserController",
      "home" => "AssetController"
};

$route.="Action";
$object = new $controller();
$object->$route();
;
require_once __DIR__ . "/config.php";
//require_once __DIR__ . "/../src/Service/ConnectionCreator.php";
require_once __DIR__ . "/../view/header.html";
require_once __DIR__ . "/../view/footer.html";

?>


