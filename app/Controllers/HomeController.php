<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

/**
 * Home Controller
 * 
 * Handles public-facing pages like homepage, about, and contact
 */
class HomeController extends Controller
{
    /**
     * Display the homepage
     * 
     * @return string Rendered view
     */
    public function index()
    {
        return View::render('home/index', [
            'title' => 'Welcome to PhatCMS',
            'content' => 'A lightweight OOP MVC CMS'
        ]);
    }

    /**
     * Display the about page
     * 
     * @return string Rendered view
     */
    public function about()
    {
        return View::render('home/about', [
            'title' => 'About Us',
            'content' => 'Learn more about PhatCMS'
        ]);
    }

    /**
     * Display the contact page
     * 
     * @return string Rendered view
     */
    public function contact()
    {
        return View::render('home/contact', [
            'title' => 'Contact Us'
        ]);
    }

    /**
     * Handle contact form submission
     * 
     * Validates and processes the contact form data
     * 
     * @return string Rendered view with success or error message
     */
    public function submitContact()
    {
        // Validate input
        $formData = $this->validateContactForm();
        
        // If validation failed
        if (!$formData['isValid']) {
            return View::render('home/contact', [
                'title' => 'Contact Us',
                'error' => 'Please fill in all fields correctly',
                'formData' => $_POST
            ]);
        }
        
        // Process contact form (e.g., send email, store in database)
        $success = $this->processContactForm($formData);
        
        return View::render('home/contact', [
            'title' => 'Contact Us',
            'success' => 'Your message has been sent!'
        ]);
    }
    
    /**
     * Validate contact form data
     * 
     * @return array Validation result with sanitized data and validity flag
     */
    private function validateContactForm(): array
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        
        return [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'isValid' => ($name && $email && $message)
        ];
    }
    
    /**
     * Process validated contact form data
     * 
     * @param array $formData Validated form data
     * @return bool True if processed successfully
     */
    private function processContactForm(array $formData): bool
    {
        // Here you would implement the actual form processing
        // Such as sending an email or storing in database
        
        // For now, just return true to simulate success
        return true;
    }
} 