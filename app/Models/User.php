<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';
    
    /**
     * Validate the user model
     */
    public function validate(): bool
    {
        $this->errors = [];
        
        // Validate name
        if (empty($this->name)) {
            $this->addError('name', 'Name is required');
        } elseif (strlen($this->name) < 3) {
            $this->addError('name', 'Name must be at least 3 characters');
        }
        
        // Validate email
        if (empty($this->email)) {
            $this->addError('email', 'Email is required');
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Email is invalid');
        }
        
        // Check if email is already taken
        if (!empty($this->email)) {
            $existingUser = self::firstWhere('email', $this->email);
            if ($existingUser && (!$this->id || $existingUser->id !== $this->id)) {
                $this->addError('email', 'Email is already taken');
            }
        }
        
        // Validate password if it's a new user or password is being updated
        if (!isset($this->id) || isset($this->attributes['password'])) {
            if (empty($this->password)) {
                $this->addError('password', 'Password is required');
            } elseif (strlen($this->password) < 6) {
                $this->addError('password', 'Password must be at least 6 characters');
            }
        }
        
        return !$this->hasErrors();
    }
    
    /**
     * Hash the user's password
     */
    public function hashPassword()
    {
        if (isset($this->attributes['password'])) {
            $this->attributes['password'] = password_hash($this->password, PASSWORD_DEFAULT);
        }
        
        return $this;
    }
    
    /**
     * Verify a password against the user's password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    /**
     * Save the user
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        $this->hashPassword();
        
        return parent::save();
    }
    
    /**
     * Find a user by email
     */
    public static function findByEmail(string $email)
    {
        return self::firstWhere('email', $email);
    }
} 