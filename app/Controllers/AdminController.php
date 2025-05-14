<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Post;

class AdminController extends Controller
{
    /**
     * Constructor with auth check
     */
    public function __construct()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->response()->redirect('/login');
        }
    }
    
    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        // Count posts
        $posts = Post::all();
        $postCount = count($posts);
        
        return View::render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'userName' => $_SESSION['user_name'] ?? 'Admin',
            'postCount' => $postCount
        ]);
    }
    
    /**
     * List all posts
     */
    public function posts()
    {
        // Get page from query string
        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        
        // Get posts with pagination
        $pagination = Post::paginate($page, 10);
        
        return View::render('admin/posts/index', [
            'title' => 'Manage Posts',
            'posts' => $pagination['items'],
            'pagination' => $pagination
        ]);
    }
    
    /**
     * Show create post form
     */
    public function createPost()
    {
        return View::render('admin/posts/create', [
            'title' => 'Create Post'
        ]);
    }
    
    /**
     * Store a new post
     */
    public function storePost()
    {
        $title = $this->request()->input('title');
        $content = $this->request()->input('content');
        
        // Validate input
        if (empty($title) || empty($content)) {
            return View::render('admin/posts/create', [
                'title' => 'Create Post',
                'error' => 'Title and content are required',
                'formData' => $_POST
            ]);
        }
        
        // Create post
        $post = Post::create([
            'title' => $title,
            'content' => $content,
            'user_id' => $_SESSION['user_id']
        ]);
        
        if (!$post->id) {
            return View::render('admin/posts/create', [
                'title' => 'Create Post',
                'error' => 'Failed to create post',
                'formData' => $_POST
            ]);
        }
        
        // Set success message
        $_SESSION['success'] = 'Post created successfully';
        
        // Redirect to posts list
        return $this->redirect('/admin/posts');
    }
    
    /**
     * Show edit post form
     */
    public function editPost($id)
    {
        // Find post
        $post = Post::find($id);
        
        if (!$post) {
            $_SESSION['error'] = 'Post not found';
            return $this->redirect('/admin/posts');
        }
        
        return View::render('admin/posts/edit', [
            'title' => 'Edit Post',
            'post' => $post
        ]);
    }
    
    /**
     * Update a post
     */
    public function updatePost($id)
    {
        // Find post
        $post = Post::find($id);
        
        if (!$post) {
            $_SESSION['error'] = 'Post not found';
            return $this->redirect('/admin/posts');
        }
        
        $title = $this->request()->input('title');
        $content = $this->request()->input('content');
        
        // Validate input
        if (empty($title) || empty($content)) {
            return View::render('admin/posts/edit', [
                'title' => 'Edit Post',
                'error' => 'Title and content are required',
                'post' => $post
            ]);
        }
        
        // Update post
        $success = $post->update([
            'title' => $title,
            'content' => $content
        ]);
        
        if (!$success) {
            return View::render('admin/posts/edit', [
                'title' => 'Edit Post',
                'error' => 'Failed to update post',
                'post' => $post
            ]);
        }
        
        // Set success message
        $_SESSION['success'] = 'Post updated successfully';
        
        // Redirect to posts list
        return $this->redirect('/admin/posts');
    }
    
    /**
     * Delete a post
     */
    public function deletePost($id)
    {
        // Find post
        $post = Post::find($id);
        
        if (!$post) {
            $_SESSION['error'] = 'Post not found';
            return $this->redirect('/admin/posts');
        }
        
        // Delete post
        $success = $post->delete();
        
        if (!$success) {
            $_SESSION['error'] = 'Failed to delete post';
        } else {
            $_SESSION['success'] = 'Post deleted successfully';
        }
        
        // Redirect to posts list
        return $this->redirect('/admin/posts');
    }
} 