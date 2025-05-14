<?php

namespace App\Core;

/**
 * Main Application Class
 * 
 * This class serves as the central point of the application,
 * handling routing, requests, responses, and error management.
 */
class Application
{
    /**
     * @var Router The router instance
     */
    protected Router $router;
    
    /**
     * @var Request The request instance
     */
    protected Request $request;
    
    /**
     * @var Response The response instance
     */
    protected Response $response;
    
    /**
     * @var Application The singleton instance of the application
     */
    protected static Application $instance;
    
    /**
     * Application constructor
     * 
     * Initializes the application components and loads routes
     * 
     * @return void
     */
    public function __construct()
    {
        self::$instance = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        
        // Load routes
        require_once APP_ROOT . '/routes/web.php';
    }
    
    /**
     * Get the application instance
     * 
     * @return Application The singleton instance
     */
    public static function getInstance(): Application
    {
        return self::$instance;
    }
    
    /**
     * Run the application
     * 
     * Resolves the current route and handles any exceptions
     * 
     * @return void
     */
    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Handle exceptions
     * 
     * Logs the error and displays an appropriate error page
     * based on the current environment
     * 
     * @param \Exception $e The exception to handle
     * @return void
     */
    protected function handleException(\Exception $e)
    {
        // Log the error
        $this->logError($e);
        
        // Display error page based on environment
        if (ENV === 'development') {
            $this->showDevelopmentError($e);
        } else {
            $this->showProductionError();
        }
    }
    
    /**
     * Log an error to the error log
     * 
     * @param \Exception $e The exception to log
     * @return void
     */
    protected function logError(\Exception $e)
    {
        error_log($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    }
    
    /**
     * Display detailed error information in development environment
     * 
     * @param \Exception $e The exception to display
     * @return void
     */
    protected function showDevelopmentError(\Exception $e)
    {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<p>File: ' . $e->getFile() . '</p>';
        echo '<p>Line: ' . $e->getLine() . '</p>';
        echo '<h2>Stack Trace:</h2>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
    /**
     * Display a user-friendly error page in production environment
     * 
     * @return void
     */
    protected function showProductionError()
    {
        echo View::render('errors/500', [
            'message' => 'An error occurred. Please try again later.'
        ]);
    }
    
    /**
     * Get the request instance
     * 
     * @return Request The current request object
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * Get the response instance
     * 
     * @return Response The current response object
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
} 