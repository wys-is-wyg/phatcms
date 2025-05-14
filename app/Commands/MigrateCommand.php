<?php

namespace App\Commands;

use App\Core\Database;

class MigrateCommand
{
    /**
     * Run migrations
     */
    public function run(array $args = [])
    {
        echo "Running migrations...\n";
        
        // Create migrations table if it doesn't exist
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS `migrations` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `migration` varchar(255) NOT NULL,
              `batch` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Get the last batch number
        $lastBatch = $db->fetch("SELECT MAX(batch) as batch FROM migrations");
        $batch = $lastBatch['batch'] ?? 0;
        $batch++;
        
        // Get all migration files from the database/migrations directory
        $migrationFiles = glob(dirname(__DIR__, 2) . '/database/migrations/*.php');
        $migrationsRun = 0;
        
        foreach ($migrationFiles as $file) {
            // Extract the migration name from the file path
            $filename = basename($file);
            
            // Check if migration has already been run
            $exists = $db->fetch("SELECT * FROM migrations WHERE migration = ?", [$filename]);
            
            if (!$exists) {
                echo "Processing migration file: $filename\n";
                
                // Execute the migration file in a new scope to avoid variable collisions
                $migration = $this->executeMigrationFile($file);
                
                if (!$migration) {
                    echo "Error: Could not execute migration: $filename\n";
                    continue;
                }
                
                // Run the up method
                $migration->up();
                
                // Record migration
                $db->query(
                    "INSERT INTO migrations (migration, batch) VALUES (?, ?)",
                    [$filename, $batch]
                );
                
                echo "Migrated: $filename\n";
                $migrationsRun++;
            }
        }
        
        if ($migrationsRun === 0) {
            echo "Nothing to migrate.\n";
        } else {
            echo "Migrations complete. Ran $migrationsRun migrations.\n";
        }
    }
    
    /**
     * Rollback migrations
     */
    public function rollback(array $args = [])
    {
        echo "Rolling back migrations...\n";
        
        $db = Database::getInstance();
        
        // Check if migrations table exists
        try {
            $db->query("SELECT 1 FROM migrations LIMIT 1");
        } catch (\Exception $e) {
            echo "No migrations to rollback.\n";
            return;
        }
        
        // Get the last batch
        $lastBatch = $db->fetch("SELECT MAX(batch) as batch FROM migrations");
        $batch = $lastBatch['batch'] ?? 0;
        
        if ($batch === 0) {
            echo "No migrations to rollback.\n";
            return;
        }
        
        // Get migrations from the last batch
        $migrations = $db->fetchAll("SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC", [$batch]);
        
        if (empty($migrations)) {
            echo "No migrations to rollback.\n";
            return;
        }
        
        foreach ($migrations as $migration) {
            $file = dirname(__DIR__, 2) . '/database/migrations/' . $migration['migration'];
            
            if (!file_exists($file)) {
                echo "Warning: Migration file not found: {$migration['migration']}\n";
                continue;
            }
            
            // Execute the migration file in a new scope
            $migrationInstance = $this->executeMigrationFile($file);
            
            if (!$migrationInstance) {
                echo "Error: Could not execute migration: {$migration['migration']}\n";
                continue;
            }
            
            // Run the down method
            $migrationInstance->down();
            
            // Remove migration record
            $db->query("DELETE FROM migrations WHERE id = ?", [$migration['id']]);
            
            echo "Rolled back: {$migration['migration']}\n";
        }
        
        echo "Rollback complete. Rolled back " . count($migrations) . " migrations.\n";
    }
    
    /**
     * Execute a migration file and return the migration object
     */
    private function executeMigrationFile(string $file)
    {
        // Include the file in a closure to avoid variable collisions
        $migrationClass = null;
        
        // Capture the classes defined before including the file
        $beforeClasses = get_declared_classes();
        
        // Include the file
        include_once $file;
        
        // Capture the classes defined after including the file
        $afterClasses = get_declared_classes();
        
        // Find the new classes defined in the file
        $newClasses = array_diff($afterClasses, $beforeClasses);
        
        // Look for a class that has up() and down() methods
        foreach ($newClasses as $class) {
            $reflectionClass = new \ReflectionClass($class);
            if ($reflectionClass->hasMethod('up') && $reflectionClass->hasMethod('down')) {
                $migrationClass = $class;
                break;
            }
        }
        
        if ($migrationClass) {
            return new $migrationClass();
        }
        
        return null;
    }
} 