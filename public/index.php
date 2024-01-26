<?php

use AdasFinance\Route\Router;

require '../vendor/autoload.php';
require_once __DIR__ . "/../view/header.html";

session_start();
Router::execute();

require_once __DIR__ . "/../view/footer.html";
?>


