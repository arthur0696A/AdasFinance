<?php
namespace AdasFinance\Route;

use Exception;

class Router
{
    private const CONTROLLER_NAMESPACE = 'AdasFinance\\Controller';
    private const ROOT_NAMESPACE = __DIR__ . '../../../';

    private const PUBLIC_ROUTES = [
        '/login_submit',
        '/signup',
        '/signup_submit'
    ];
    
    private const FETCH_ROUTES = [
        '/search_by_symbol',
        '/user_asset_goal_percentage'
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
                '/logout' => fn () => self::load('UserController', 'logout'),
                '/home' => fn () => self::load('UserAssetController', 'home'),
                '/search_by_symbol' => fn () => self::load('AssetController', 'searchBySymbol'),
                // '/' => fn () => self::load('fazer 404'),
            ],

            'post' => [
                '/login_submit' => fn () => self::load('UserController', 'loginSubmit'),
                '/signup_submit' => fn () => self::load('UserController', 'signupSubmit'),
                '/user_asset_goal_percentage' => fn () => self::load('UserAssetController', 'userAssetGoalPercentage'),
                '/user_asset_save' => fn () => self::load('UserAssetController', 'userAssetSave'),
                '/register_transaction' => fn () => self::load('UserAssetController', 'registerTransaction'),
                '/synchronize_user_assets' => fn () => self::load('UserController', 'synchronizeUserAssets'),
            ],

            'put' => [
                
            ],

            'delete' => [
                '/user_asset_delete' => fn () => self::load('UserAssetController', 'userAssetDelete'),
            ],
        ] ;
    }

    public static function execute()
    {
        try {
            $routes = self::routes();
            $requestMethod = Request::get();
            $uri = Uri::get('path');

            self::includeHeader($uri);
            
            if (!isset($routes[$requestMethod]) || !array_key_exists($uri, $routes[$requestMethod])) {
                throw new Exception('Route not found');
            }
    
            self::handleAuthenticationRedirects($requestMethod, $uri);
            $router = $routes[$requestMethod][$uri];
    
            if (!is_callable($router)) {
                throw new Exception("Route {$uri} is not callable");
            }
    
            $router();
    
            self::includeFooter($uri);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    private static function includeHeader($uri)
    {
        if (!in_array($uri, self::FETCH_ROUTES)) {
            require_once self::ROOT_NAMESPACE . 'view/header.html';
        }
    }

    private static function includeFooter($uri)
    {
        if (!in_array($uri, self::FETCH_ROUTES)) {
            require_once self::ROOT_NAMESPACE . 'view/footer.html';
        }
    }

    private static function handleAuthenticationRedirects(&$requestMethod, &$uri)
    {
        if (!isset($_SESSION['user']) && !in_array($uri, self::PUBLIC_ROUTES)) {
            $requestMethod = "get";
            $uri = '/login';
        }

        if (isset($_SESSION['user']) && $uri === '/login') {
            $requestMethod = "get";
            $uri = '/home';
        }
    }
}

?>