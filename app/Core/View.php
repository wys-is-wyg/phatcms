<?php

namespace App\Core;

class View
{
    /**
     * Render a view with data
     */
    public static function render(string $view, array $data = [])
    {
        $layoutContent = self::layoutContent();
        $viewContent = self::viewContent($view, $data);
        
        // Replace {{content}} in the layout with the view content
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
    
    /**
     * Render a view without layout
     */
    public static function renderPartial(string $view, array $data = [])
    {
        return self::viewContent($view, $data);
    }
    
    /**
     * Get the layout content
     */
    protected static function layoutContent()
    {
        // Start output buffering
        ob_start();
        
        // Include the layout file
        include_once VIEWS_PATH . '/layouts/main.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Get the view content
     */
    protected static function viewContent(string $view, array $data)
    {
        // Extract data to make variables available in the view
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = VIEWS_PATH . '/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include_once $viewPath;
        } else {
            echo "View {$view} not found";
        }
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    public static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
} 