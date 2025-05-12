<?php

namespace App\Core;

class Router
{
    protected $routes = [];
    protected $controllersNamespace = 'App\\Controllers\\';

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

        if (str_starts_with($uri, "/post/")) {
            $postSegment = substr($uri, strlen("/post/"));
            $parts = explode('-', $postSegment, 2);
            $postId = (int)($parts[0] ?? null);

            if (is_numeric($postId)) {
                $_GET["id"] = $postId;
                $this->callAction('PostController', 'show');
                return;
            }
        }

        $this->callAction('ErrorController', 'notFound');
    }

    protected function callAction(string $controller, string $action): void
    {
        $controllerClass = $this->controllersNamespace . $controller;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller class {$controllerClass} not found");
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $action)) {
            throw new \RuntimeException("Method {$action} not found in controller {$controllerClass}");
        }

        $controllerInstance->$action();
    }
}
