<?php

namespace App\Core;

class Response
{
    /**
     * Set the HTTP status code
     */
    public function setStatusCode(int $code)
    {
        http_response_code($code);
        return $this;
    }
    
    /**
     * Redirect to a URL
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Return JSON response
     */
    public function json($data, int $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Set a response header
     */
    public function setHeader(string $name, string $value)
    {
        header("$name: $value");
        return $this;
    }
    
    /**
     * Return a file download response
     */
    public function download(string $filePath, string $name = null)
    {
        if (!file_exists($filePath)) {
            $this->setStatusCode(404);
            return false;
        }
        
        $filename = $name ?? basename($filePath);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
} 