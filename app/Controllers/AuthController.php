<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function loginForm()
    {
        return View::render('auth/login', [
            'title' => 'Login'
        ]);
    }
    
    /**
     * Handle login
     */
    public function login()
    {
        $email = $this->request()->input('email');
        $password = $this->request()->input('password');
        
        // Validate input
        if (empty($email) || empty($password)) {
            return View::render('auth/login', [
                'title' => 'Login',
                'error' => 'Email and password are required',
                'email' => $email
            ]);
        }
        
        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user || !$user->verifyPassword($password)) {
            return View::render('auth/login', [
                'title' => 'Login',
                'error' => 'Invalid email or password',
                'email' => $email
            ]);
        }
        
        // Set user session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        
        // Redirect to dashboard
        return $this->redirect('/admin');
    }
    
    /**
     * Show registration form
     */
    public function registerForm()
    {
        return View::render('auth/register', [
            'title' => 'Register'
        ]);
    }
    
    /**
     * Handle registration
     */
    public function register()
    {
        $name = $this->request()->input('name');
        $email = $this->request()->input('email');
        $password = $this->request()->input('password');
        $passwordConfirm = $this->request()->input('password_confirm');
        
        // Validate passwords match
        if ($password !== $passwordConfirm) {
            return View::render('auth/register', [
                'title' => 'Register',
                'error' => 'Passwords do not match',
                'name' => $name,
                'email' => $email
            ]);
        }
        
        // Create new user
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        
        // Save user
        if (!$user->save()) {
            return View::render('auth/register', [
                'title' => 'Register',
                'error' => $user->getErrors() ? reset($user->getErrors())[0] : 'Registration failed',
                'name' => $name,
                'email' => $email
            ]);
        }
        
        // Set success message
        $_SESSION['success'] = 'Registration successful! You can now log in.';
        
        // Redirect to login
        return $this->redirect('/login');
    }
    
    /**
     * Handle logout
     */
    public function logout()
    {
        // Clear session
        session_unset();
        session_destroy();
        
        // Redirect to home
        return $this->redirect('/');
    }
} 