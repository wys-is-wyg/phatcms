# PhatCMS

A lightweight OOP MVC CMS using PHP, Vanilla JS, HTMX, and Tailwind CSS.

## Features

- MVC architecture
- OOP design
- Routing system
- View templating
- Form handling and validation
- AJAX support
- HTMX integration
- Responsive design with Tailwind CSS
- Database abstraction layer
- Migration system
- Seeding system

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and NPM

## Installation

1. Clone the repository:

```
git clone https://github.com/yourusername/phatcms.git
cd phatcms
```

2. Install PHP dependencies:

```
composer install
```

3. Install JavaScript dependencies:

```
npm install
```

4. Copy the environment file:

```
cp .env.example .env
```

5. Update the `.env` file with your database credentials.

6. Run database migrations:

```
php phatcms migrate
```

7. Seed the database:

```
php phatcms seed
```

8. Build assets:

```
npm run dev
```

9. Set appropriate permissions:

```
chmod +x phatcms
chmod -R 777 storage
```

## Database Setup

The CMS uses MySQL as its database. You need to create a database and update the `.env` file with your database credentials:

```
DB_HOST=localhost
DB_NAME=phatcms
DB_USER=root
DB_PASS=your_password
```

Then run the migrations and seeders to set up the database schema and initial data:

```
php phatcms migrate
php phatcms seed
```

This will create the necessary tables and an admin user with the following credentials:

- Email: admin@example.com
- Password: password

## Directory Structure

- **app/** - Core application code
  - **Commands/** - Console commands
  - **Controllers/** - Controller classes
  - **Core/** - Core framework classes
  - **Helpers/** - Helper functions
  - **Models/** - Model classes
  - **Services/** - Service classes
- **bootstrap/** - App initialization
- **config/** - Configuration files
- **database/** - Database migrations/seeds
  - **migrations/** - Database migrations
  - **seeds/** - Database seeders
- **public/** - Publicly accessible files
  - **index.php** - Entry point
  - **assets/** - Compiled assets
- **resources/** - Pre-compiled assets
  - **css/** - CSS files
  - **js/** - JavaScript files
  - **views/** - View templates
- **routes/** - Route definitions
- **storage/** - Storage directory
- **tests/** - Test files

## Usage

### Development

For development, run:

```
npm run watch
```

This will watch for changes in your CSS and JS files and recompile them automatically.

### Production

For production, run:

```
npm run prod
```

This will minify and optimize your assets for production.

## Console Commands

PhatCMS comes with a console application that allows you to run various commands:

```
php phatcms <command> [action] [arguments]
```

Available commands:

- `migrate` - Run database migrations

  - `run` - Run pending migrations (default)
  - `rollback` - Rollback the last batch of migrations

- `seed` - Run database seeders
  - `run` - Run all seeders (default)

## License

MIT
