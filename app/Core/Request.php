<?php

namespace App\Core;

class Request
{
    private array $routeParams = [];
    
    /**
     * Get the current path
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }
    
    /**
     * Get the request method
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Get all request data
     */
    public function all()
    {
        $body = [];
        
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        return $body;
    }
    
    /**
     * Get a specific request value
     */
    public function input(string $key, $default = null)
    {
        $body = $this->all();
        return $body[$key] ?? $default;
    }
    
    /**
     * Set route parameters
     */
    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
        return $this;
    }
    
    /**
     * Get route parameters
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }
    
    /**
     * Get a specific route parameter
     */
    public function getRouteParam(string $key, $default = null)
    {
        return $this->routeParams[$key] ?? $default;
    }
    
    /**
     * Check if the request is AJAX
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Check if the request has a specific header
     */
    public function hasHeader(string $name)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return isset($_SERVER[$name]);
    }
    
    /**
     * Get a specific header
     */
    public function header(string $name, $default = null)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? $default;
    }
} 