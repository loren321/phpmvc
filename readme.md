This is a PHP MVC framework made with publicly available components.
Components I used:
* **[Altorouter](http://altorouter.com/)** for routing
* **[Symfony HttpFoundation component](http://symfony.com/doc/current/components/http_foundation/introduction.html)** for handling requests
* **[Laravel database component](https://github.com/illuminate/database)** for communication with the Database

## Requirements
* PHP 5.6
* [Composer](https://getcomposer.org/)
* web server with mod_rewrite enabled (I used Apache on Ubuntu 16.04)

## Installation
* run `composer install` to install all the components and generate autoloading script
* set the `public/` directory as a document root on your server
* edit your database connection config in `framework/database.php`
* run `php -f App/database/schema.php`

## Directory structure
* `App/`
    * `Models/` - Models go here. Namespace: `\App\Models\`
    * `Controllers/` - This is where the controllers go. Namespace: `\App\Controllers\`
    * `Views/` - Views should be here.
    * `database/` - database schema goes here
        * `schema.php` - Database schema
    * `routes.php` - This is where the routes are defined. Example route: `$app->router->map('GET', '/user/[i:id]', 'ControllerName#action');`
* `framework/` - Here is here framework core.
    * `App.php` - Application class. You shouldn't touch this class.
    * `database.php` - Database connection.
* `public/` - set this directory as document root.
    * `index.php` - front controller. All requests are forwarded to this file
    * `.htaccess`
    
