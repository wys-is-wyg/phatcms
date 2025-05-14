<?php

use App\Core\Database;

class CreateInitialTables
{
    /**
     * Run the migration
     */
    public function up()
    {
        $db = Database::getInstance();
        
        // Create users table
        $db->query("
            CREATE TABLE IF NOT EXISTS `users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `password` varchar(255) NOT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Create posts table
        $db->query("
            CREATE TABLE IF NOT EXISTS `posts` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `title` varchar(255) NOT NULL,
              `content` text NOT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Create pages table
        $db->query("
            CREATE TABLE IF NOT EXISTS `pages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `title` varchar(255) NOT NULL,
              `slug` varchar(255) NOT NULL,
              `content` text NOT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `slug` (`slug`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Create settings table
        $db->query("
            CREATE TABLE IF NOT EXISTS `settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `key` varchar(255) NOT NULL,
              `value` text NOT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Insert default settings
        $db->query("
            INSERT INTO `settings` (`key`, `value`) VALUES
            ('site_name', 'PhatCMS'),
            ('site_description', 'A lightweight OOP MVC CMS'),
            ('site_keywords', 'cms, php, mvc, htmx, tailwind'),
            ('posts_per_page', '10'),
            ('theme', 'default'),
            ('maintenance_mode', '0');
        ");
    }
    
    /**
     * Reverse the migration
     */
    public function down()
    {
        $db = Database::getInstance();
        
        // Drop tables in reverse order
        $db->query("DROP TABLE IF EXISTS `settings`;");
        $db->query("DROP TABLE IF EXISTS `pages`;");
        $db->query("DROP TABLE IF EXISTS `posts`;");
        $db->query("DROP TABLE IF EXISTS `users`;");
    }
} 