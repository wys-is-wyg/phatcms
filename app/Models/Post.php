<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Post extends Model
{
    protected static string $table = 'posts';
    
    /**
     * Validate the post model
     */
    public function validate(): bool
    {
        $this->errors = [];
        
        // Validate title
        if (empty($this->title)) {
            $this->addError('title', 'Title is required');
        } elseif (strlen($this->title) < 3) {
            $this->addError('title', 'Title must be at least 3 characters');
        }
        
        // Validate content
        if (empty($this->content)) {
            $this->addError('content', 'Content is required');
        } elseif (strlen($this->content) < 10) {
            $this->addError('content', 'Content must be at least 10 characters');
        }
        
        return !$this->hasErrors();
    }
    
    /**
     * Get the post author
     */
    public function author()
    {
        if (!isset($this->user_id)) {
            return null;
        }
        
        return User::find($this->user_id);
    }
    
    /**
     * Get posts with pagination
     */
    public static function paginate(int $page = 1, int $perPage = 10): array
    {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        
        $total = $db->fetch("SELECT COUNT(*) as count FROM " . static::$table)['count'];
        $items = $db->fetchAll(
            "SELECT * FROM " . static::$table . " ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
        $posts = [];
        foreach ($items as $item) {
            $posts[] = new static($item);
        }
        
        return [
            'items' => $posts,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Search posts
     */
    public static function search(string $query): array
    {
        $db = Database::getInstance();
        $searchTerm = '%' . $query . '%';
        
        $items = $db->fetchAll(
            "SELECT * FROM " . static::$table . " WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC",
            [$searchTerm, $searchTerm]
        );
        
        $posts = [];
        foreach ($items as $item) {
            $posts[] = new static($item);
        }
        
        return $posts;
    }
    
    /**
     * Create a new post
     */
    public static function create(array $data): self
    {
        // Set created_at and updated_at
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $post = new static($data);
        $post->save();
        
        return $post;
    }
    
    /**
     * Update the post
     */
    public function update(array $data): bool
    {
        // Set updated_at
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->fill($data);
        return $this->save();
    }
} 