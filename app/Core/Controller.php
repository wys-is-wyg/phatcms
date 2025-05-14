<?php

namespace App\Core;

class Controller
{
    /**
     * Get the application instance
     */
    protected function app(): Application
    {
        return Application::getInstance();
    }
    
    /**
     * Get the request instance
     */
    protected function request(): Request
    {
        return $this->app()->getRequest();
    }
    
    /**
     * Get the response instance
     */
    protected function response(): Response
    {
        return $this->app()->getResponse();
    }
    
    /**
     * Render a view
     */
    protected function render(string $view, array $data = [])
    {
        return View::render($view, $data);
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect(string $url)
    {
        return $this->response()->redirect($url);
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, int $statusCode = 200)
    {
        return $this->response()->json($data, $statusCode);
    }
} 