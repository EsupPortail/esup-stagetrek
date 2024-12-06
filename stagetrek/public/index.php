<?php
//phpinfo();
//die();


define('REQUEST_MICROTIME', microtime(true));

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Laminas\Mvc\Application::init(require 'config/application.config.php')->run();
