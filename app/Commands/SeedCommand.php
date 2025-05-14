<?php

namespace App\Commands;

class SeedCommand
{
    /**
     * Run seeders
     */
    public function run(array $args = [])
    {
        echo "Running seeders...\n";
        
        // Get all seeder files from the database/seeds directory
        $seederFiles = glob(dirname(__DIR__, 2) . '/database/seeds/*.php');
        $seedersRun = 0;
        
        foreach ($seederFiles as $file) {
            // Extract the seeder name from the file path
            $filename = basename($file);
            
            echo "Processing seeder file: $filename\n";
            
            // Execute the seeder file and return the seeder object
            $seeder = $this->executeSeederFile($file);
            
            if (!$seeder) {
                echo "Error: Could not execute seeder: $filename\n";
                continue;
            }
            
            // Run the seeder
            $seeder->run();
            
            echo "Seeded: $filename\n";
            $seedersRun++;
        }
        
        if ($seedersRun === 0) {
            echo "No seeders to run.\n";
        } else {
            echo "Seeding complete. Ran $seedersRun seeders.\n";
        }
    }
    
    /**
     * Execute a seeder file and return the seeder object
     */
    private function executeSeederFile(string $file)
    {
        // Capture the classes defined before including the file
        $beforeClasses = get_declared_classes();
        
        // Include the file
        include_once $file;
        
        // Capture the classes defined after including the file
        $afterClasses = get_declared_classes();
        
        // Find the new classes defined in the file
        $newClasses = array_diff($afterClasses, $beforeClasses);
        
        // Look for a class that has a run() method
        foreach ($newClasses as $class) {
            $reflectionClass = new \ReflectionClass($class);
            if ($reflectionClass->hasMethod('run')) {
                $seederClass = $class;
                break;
            }
        }
        
        if (isset($seederClass)) {
            return new $seederClass();
        }
        
        return null;
    }
} 