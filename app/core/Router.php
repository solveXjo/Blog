<?php

class Router
{
    protected $routes = [];
    public function getRoutes(): array
    {
        return $this->routes;
    }
    public function addRoute(string $uri, string $viewPath, bool $exactMatch = true): void
    {
        $this->routes[] = [
            'uri' => $uri,
            'view' => $viewPath,
            'exact' => $exactMatch
        ];
    }

    public function addDynamicRoute(string $prefix, string $viewPath, string $paramName): void
    {
        $this->routes[] = [
            'prefix' => $prefix,
            'view' => $viewPath,
            'param' => $paramName,
            'dynamic' => true
        ];
    }

    public function route(string $uri): void
    {
        foreach ($this->routes as $route) {
            if (isset($route['exact']) && $route['exact'] && $route['uri'] === $uri) {
                $this->loadView($route['view']);
                return;
            }
        }

        foreach ($this->routes as $route) {
            if (isset($route['dynamic']) && $route['dynamic'] && str_starts_with($uri, $route['prefix'])) {
                $paramValue = substr($uri, strlen($route['prefix']));
                if (!empty($paramValue)) {
                    $_GET[$route['param']] = $paramValue;
                    $this->loadView($route['view']);
                    return;
                }
            }
        }


        if (str_starts_with($uri, "/post/")) {
            $postSegment = substr($uri, strlen("/post/"));
            $parts = explode('-', $postSegment, 2);
            $postId = (int)($parts[0] ?? null);

            if (is_numeric($postId)) {
                $_GET["id"] = $postId;
                $this->loadView(__DIR__ . '/' . '../../resources/views/single-post.view.php');
                return;
            }
        }

        $this->loadView('resources/views/index.view.php');
    }

    protected function loadView(string $viewPath): void
    {
        if (!str_starts_with($viewPath, '/')) {
            $viewPath = ltrim($viewPath, '/');
        }

        $viewPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $viewPath);

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            http_response_code(404);
            echo "Error: View file not found at: " . htmlspecialchars($viewPath);
            echo "<pre>Current working directory: " . getcwd() . "</pre>";
            exit();
        }
    }
}
