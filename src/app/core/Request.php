<?php

class Request
{
    protected $uri;
    protected $method;
    protected $params = [];

    public function __construct()
    {
        $this->uri = $this->parseUri();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->params = array_merge($_GET, $_POST);
    }

    /**
     * Parse the URI from the request
     */
    protected function parseUri(): string
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        // Normalize URI
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        return $uri;
    }

    /**
     * Get the request URI
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get a parameter value
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get all parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set a parameter value
     */
    public function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    /**
     * Check if a parameter exists
     */
    public function hasParam(string $key): bool
    {
        return isset($this->params[$key]);
    }

    /**
     * Get a value from $_GET
     */
    public function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a value from $_POST
     */
    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get a value from $_SERVER
     */
    public function server(string $key, $default = null)
    {
        return $_SERVER[$key] ?? $default;
    }
}
