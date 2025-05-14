<?php

namespace App\Core;

/**
 * Router Class
 * 
 * Handles routing of requests to appropriate controllers or callbacks
 * Supports static and dynamic routes with parameters
 */
class Router
{
    /**
     * @var array Array of registered routes
     */
    protected static array $routes = [];
    
    /**
     * @var Request The current request instance
     */
    protected Request $request;
    
    /**
     * @var Response The response instance
     */
    protected Response $response;
    
    /**
     * Router constructor
     * 
     * @param Request $request The request instance
     * @param Response $response The response instance
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    
    /**
     * Register a GET route
     * 
     * @param string $path The route path
     * @param mixed $callback The callback function or controller action
     * @return void
     */
    public static function get(string $path, $callback)
    {
        self::$routes['get'][$path] = $callback;
    }
    
    /**
     * Register a POST route
     * 
     * @param string $path The route path
     * @param mixed $callback The callback function or controller action
     * @return void
     */
    public static function post(string $path, $callback)
    {
        self::$routes['post'][$path] = $callback;
    }
    
    /**
     * Register a PUT route
     * 
     * @param string $path The route path
     * @param mixed $callback The callback function or controller action
     * @return void
     */
    public static function put(string $path, $callback)
    {
        self::$routes['put'][$path] = $callback;
    }
    
    /**
     * Register a DELETE route
     * 
     * @param string $path The route path
     * @param mixed $callback The callback function or controller action
     * @return void
     */
    public static function delete(string $path, $callback)
    {
        self::$routes['delete'][$path] = $callback;
    }
    
    /**
     * Resolve the current route
     * 
     * @return mixed The response from the route handler
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = self::$routes[$method][$path] ?? false;
        
        // If route not found
        if ($callback === false) {
            $callback = $this->findDynamicRoute($path, $method);
            
            // If still not found, return 404
            if ($callback === false) {
                return $this->handleNotFound();
            }
        }
        
        return $this->executeCallback($callback);
    }
    
    /**
     * Find a matching dynamic route
     * 
     * @param string $path The current path
     * @param string $method The HTTP method
     * @return mixed The callback if found, false otherwise
     */
    protected function findDynamicRoute(string $path, string $method)
    {
        foreach (self::$routes[$method] ?? [] as $route => $handler) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $path, $matches)) {
                $this->request->setRouteParams(array_slice($matches, 1));
                return $handler;
            }
        }
        
        return false;
    }
    
    /**
     * Handle 404 Not Found responses
     * 
     * @return string The 404 page content
     */
    protected function handleNotFound()
    {
        $this->response->setStatusCode(404);
        return View::render('errors/404');
    }
    
    /**
     * Execute the route callback
     * 
     * @param mixed $callback The route callback
     * @return mixed The response from the callback
     */
    protected function executeCallback($callback)
    {
        // If callback is string, render view
        if (is_string($callback)) {
            return View::render($callback);
        }
        
        // If callback is array, call controller method
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            $callback = [$controller, $method];
        }
        
        return call_user_func_array($callback, [$this->request, $this->response]);
    }
    
    /**
     * Convert route with parameters to regex pattern
     * 
     * @param string $route The route with parameters
     * @return string The regex pattern
     */
    private function convertRouteToRegex(string $route): string
    {
        if (strpos($route, '{') === false) {
            return "#^$route$#";
        }
        
        return "#^" . preg_replace('/{([a-zA-Z0-9_]+)}/', '([^/]+)', $route) . "$#";
    }
} 