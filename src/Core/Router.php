<?php

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Resolve route from GET parameter or Request URI
        $rawRoute = $_GET['route'] ?? null;
        if (!$rawRoute) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $rawRoute = trim(str_replace($scriptName, '', $uri), '/');
        }

        if ($rawRoute) {
            $parsed = parse_url($rawRoute);
            $route = trim($parsed['path'] ?? '', '/');
            if (!empty($parsed['query'])) {
                parse_str($parsed['query'], $extraParams);
                $_GET = array_merge($_GET, $extraParams);
                $_REQUEST = array_merge($_REQUEST, $extraParams);
            }
        } else {
            $route = 'home';
        }

        $route = $route ?: 'home';

        if (isset($this->routes[$method][$route])) {
            [$controllerClass, $action] = $this->routes[$method][$route];
            $controller = new $controllerClass();
            $controller->$action();
        } else {
            // Fallback default routing or 404
            if (isset($this->routes['GET']['home'])) {
                [$controllerClass, $action] = $this->routes['GET']['home'];
                $controller = new $controllerClass();
                $controller->$action();
            } else {
                http_response_code(404);
                echo "<h1>404 Page Not Found</h1>";
            }
        }
    }
}
