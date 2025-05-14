<?php

namespace App\Core;

abstract class Model
{
    protected static string $table;
    protected array $attributes = [];
    protected array $errors = [];
    protected ?int $id = null;
    
    /**
     * Get the model's table name
     */
    public static function getTable(): string
    {
        return static::$table;
    }
    
    /**
     * Create a new model instance
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }
    
    /**
     * Fill the model with attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        
        if (isset($attributes['id'])) {
            $this->id = (int) $attributes['id'];
        }
        
        return $this;
    }
    
    /**
     * Get an attribute
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }
    
    /**
     * Set an attribute
     */
    public function __set(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }
    
    /**
     * Check if an attribute exists
     */
    public function __isset(string $name)
    {
        return isset($this->attributes[$name]);
    }
    
    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * Get model errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Add an error
     */
    protected function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }
    
    /**
     * Check if the model has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Validate the model
     */
    abstract public function validate(): bool;
    
    /**
     * Save the model
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        $db = Database::getInstance();
        
        try {
            if ($this->id) {
                // Update existing record
                $db->update(static::$table, $this->attributes, 'id = ?', [$this->id]);
            } else {
                // Insert new record
                $this->id = $db->insert(static::$table, $this->attributes);
                $this->attributes['id'] = $this->id;
            }
            
            return true;
        } catch (\Exception $e) {
            $this->addError('database', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete the model
     */
    public function delete(): bool
    {
        if (!$this->id) {
            $this->addError('id', 'Cannot delete a model without an ID');
            return false;
        }
        
        $db = Database::getInstance();
        
        try {
            $db->delete(static::$table, 'id = ?', [$this->id]);
            return true;
        } catch (\Exception $e) {
            $this->addError('database', $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find a model by ID
     */
    public static function find(int $id)
    {
        $db = Database::getInstance();
        $data = $db->fetch("SELECT * FROM " . static::$table . " WHERE id = ?", [$id]);
        
        if (!$data) {
            return null;
        }
        
        return new static($data);
    }
    
    /**
     * Find all models
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll("SELECT * FROM " . static::$table);
        
        $models = [];
        foreach ($rows as $row) {
            $models[] = new static($row);
        }
        
        return $models;
    }
    
    /**
     * Find models by a where condition
     */
    public static function where(string $column, $value): array
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll(
            "SELECT * FROM " . static::$table . " WHERE {$column} = ?",
            [$value]
        );
        
        $models = [];
        foreach ($rows as $row) {
            $models[] = new static($row);
        }
        
        return $models;
    }
    
    /**
     * Find first model by a where condition
     */
    public static function firstWhere(string $column, $value)
    {
        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT * FROM " . static::$table . " WHERE {$column} = ?",
            [$value]
        );
        
        if (!$row) {
            return null;
        }
        
        return new static($row);
    }
} 