<?php

use Application\Provider\Misc\EnvironnementProvider;
use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

try{
    chdir(dirname(__DIR__));
    if ( !file_exists('vendor/autoload.php')) {
        throw new RuntimeException(
            'Unable to load application.'
        );
    }

    // Setup autoloading
    include 'vendor/autoload.php';

    $appConfig = include 'config/application.config.php';

    if (file_exists('config/development.config.php')) {
        $appConfig = ArrayUtils::merge(
            $appConfig,
            include 'config/development.config.php'
        );
    }

    // Run the application!
    Application::init($appConfig)->run();

}
//Ramasse miette pour les derniéres erreurs non capturée
catch (Exception|Error|RuntimeException $e){
    $env =  ($_ENV['APP_ENV']) ?? EnvironnementProvider::PRODUCTION;
    if($env == EnvironnementProvider::DEVELOPPEMENT){
        throw $e;
    }
    if(!headers_sent()){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }
    echo "<h1>Internal Server Error</h1>";
}