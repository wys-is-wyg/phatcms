<?php

/**
 * PhatCMS - Web Routes
 * 
 * Define all web routes here
 */

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

// Public routes
Router::get('/', [HomeController::class, 'index']);
Router::get('/about', [HomeController::class, 'about']);
Router::get('/contact', [HomeController::class, 'contact']);
Router::post('/contact', [HomeController::class, 'submitContact']);

// Authentication routes
Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);
Router::get('/register', [AuthController::class, 'registerForm']);
Router::post('/register', [AuthController::class, 'register']);

// Admin routes (these will be protected by middleware)
Router::get('/admin', [AdminController::class, 'dashboard']);
Router::get('/admin/posts', [AdminController::class, 'posts']);
Router::get('/admin/posts/create', [AdminController::class, 'createPost']);
Router::post('/admin/posts/store', [AdminController::class, 'storePost']);
Router::get('/admin/posts/edit/{id}', [AdminController::class, 'editPost']);
Router::post('/admin/posts/update/{id}', [AdminController::class, 'updatePost']);
Router::post('/admin/posts/delete/{id}', [AdminController::class, 'deletePost']); 