<?php

namespace App\Core;

use App\Controllers\ErrorController;
use App\Controllers\SinglePostController;

class Router
{
    protected $routes = [];
    protected $controllersNamespace = '';

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function addRoute(string $method, string $uri, string $controller, string $action): void
    {
        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function addDynamicRoute(string $method, string $prefix, string $controller, string $action, string $paramName): void
    {
        $this->routes[$method]['dynamic'][$prefix] = [
            'controller' => $controller,
            'action' => $action,
            'param' => $paramName
        ];
    }

    public function addIdSlugRoute(string $method, string $prefix, string $controller, string $action): void
    {
        $this->routes[$method]['idslug'][$prefix] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function route(string $uri, string $method = 'GET'): void
    {
        $method = strtoupper($method);

        if (isset($this->routes[$method][$uri])) {
            $this->callAction(
                $this->routes[$method][$uri]['controller'],
                $this->routes[$method][$uri]['action']
            );
            return;
        }

        if (isset($this->routes[$method]['idslug'])) {
            foreach ($this->routes[$method]['idslug'] as $prefix => $route) {
                if (str_starts_with($uri, $prefix)) {
                    $path = substr($uri, strlen($prefix));

                    // Extract ID from the path (everything before first dash)
                    preg_match('/^(\d+)/', $path, $matches);

                    if (!empty($matches[1])) {
                        $_GET['id'] = $matches[1];

                        $this->callAction(
                            $route['controller'],
                            $route['action']
                        );
                        return;
                    }
                }
            }
        }

        if (isset($this->routes[$method]['dynamic'])) {
            foreach ($this->routes[$method]['dynamic'] as $prefix => $route) {
                if (str_starts_with($uri, $prefix)) {
                    $paramValue = substr($uri, strlen($prefix));
                    if (!empty($paramValue)) {
                        $_GET[$route['param']] = $paramValue;
                        $this->callAction($route['controller'], $route['action']);
                        return;
                    }
                }
            }
        }

        // If no route matches, show 404
        $this->callAction(ErrorController::class, 'notFound');
    }

    public function convertTitleToURL($str)
    {
        // Convert string to lowercase
        $str = strtolower($str);

        // Replace spaces with hyphens
        $str = str_replace(' ', '-', $str);

        // Remove special characters
        $str = preg_replace('/[^a-z0-9\-]/', '', $str);

        // Remove consecutive hyphens
        $str = preg_replace('/-+/', '-', $str);

        // Trim hyphens from beginning and end
        $str = trim($str, '-');

        return $str;
    }

    protected function callAction(string $controller, string $action): void
    {
        $controllerClass = $this->controllersNamespace . $controller;

        if (!class_exists($controllerClass)) {
            echo "Controller class {$controllerClass} not found";
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $action)) {
            echo "Method {$action} not found in controller {$controllerClass}";
        }

        $controllerInstance->$action();
    }
}
