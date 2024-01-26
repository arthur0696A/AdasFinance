<?php
namespace AdasFinance\Route;

use Exception;

class Router
{
    public const CONTROLLER_NAMESPACE = 'AdasFinance\\Controller';
    public const PUBLIC_ROUTES = [
        '/login_submit',
        '/signup',
        '/signup_submit'
    ];

    public static function load(string $controller, string $action)
    {
        try {
            $controllerNamespace = self::CONTROLLER_NAMESPACE . '\\' . $controller;
            $action .= "Action";

            if (!class_exists($controllerNamespace)) {
                throw new Exception("Controller {$controller} not found");
            }

            $controllerInstance = new $controllerNamespace;

            if (!method_exists($controllerInstance, $action)) {
                throw new Exception("Action {$action} not found in Controller {$controller}");
            }

            $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;

            $controllerInstance->$action((object) $data);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    public static function routes(): array
    {
        return [
            'get' => [
               
                '/login' => fn () => self::load('UserController', 'login'),
                '/signup' => fn () => self::load('UserController', 'signup'),
                '/home' => fn () => self::load('AssetController', 'home'),
                '/logout' => fn () => self::load('UserController', 'logout'),
                // '/' => fn () => self::load('fazer 404'),
            ],

            'post' => [
                '/login_submit' => fn () => self::load('UserController', 'loginSubmit'),
                '/asset_goal_percentage' => fn () => self::load('AssetController', 'assetGoalPercentage'),
            ],

            'put' => [
                '/signup_submit' => fn () => self::load('UserController', 'signupSubmit'),
            ],

            'delete' => [

            ],
        ] ;
    }

    public static function execute()
    {
        try {
            $routes = self::routes();
            $request = Request::get();
            $uri = Uri::get('path');

            if (!isset($routes[$request])) {
                throw new Exception('Route not found');
            }

            if (!array_key_exists($uri, $routes[$request])) {
                throw new Exception('Route not found');
            }

            if (!isset($_SESSION['user']) && !in_array($uri, self::PUBLIC_ROUTES)) {
                $request = "get";  
                $uri = '/login';
            }
            
            if (isset($_SESSION['user']) && $uri === '/login') {
                $request = "get";
                $uri = '/home';
            }

            $router = $routes[$request][$uri];

            if (!is_callable($router)) {
                throw new Exception("Route {$uri} is not callable");
            }

            $router();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
}

?>