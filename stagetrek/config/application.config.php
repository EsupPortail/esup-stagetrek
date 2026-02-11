<?php

use Application\Provider\Misc\EnvironnementProvider;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$modules = [
    'Laminas\Cache',
//    'Laminas\Cache\Storage\Adapter\Filesystem',
    'Laminas\Filter',
    'Laminas\Form',
    'Laminas\Hydrator',
    'Laminas\I18n',
    'Laminas\InputFilter',
    'Laminas\Log',
//    'Laminas\Mail',
    'Laminas\Mvc\I18n',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Navigation',
    'Laminas\Paginator',
    'Laminas\Router',
    'Laminas\Session',
    'Laminas\Validator',

    'DoctrineModule',
    'DoctrineORMModule',
    'ZfcUser',
    'BjyAuthorize',
    'UnicaenApp',
    'Unicaen\Console',
    'UnicaenLdap',
    'UnicaenPrivilege',
    'UnicaenAuthentification',
    'UnicaenUtilisateur',
    'UnicaenUtilisateurLdapAdapter',
    'UnicaenMail',
    'UnicaenRenderer',
    'UnicaenEvenement',
    'UnicaenEtat',
    'UnicaenTag',
    'Unicaen\BddAdmin',
    'UnicaenFichier',
    'UnicaenStorage',
    'UnicaenDbImport',
    'UnicaenIndicateur',
    'UnicaenCalendrier',
    'Application',
    'Console',
    'Evenement',
    'API',
    'BddAdmin',
    'Indicateur',
];

//Ajout de Faker qui pose pb en l'incluant depuis modules
require_once 'vendor/autoload.php';

if(!isset($_ENV['APP_ENV'])){
    $_ENV['APP_ENV'] = EnvironnementProvider::PRODUCTION;
}

$applicationEnv = $_ENV['APP_ENV'];
if ($applicationEnv == EnvironnementProvider::DEVELOPPEMENT) {
    $modules[] = 'Laminas\DeveloperTools';
//    $modules[] = 'UnicaenCode';
}


$moduleListenerOptions = [
    'config_glob_paths'    => [
        'config/autoload/{,*.}{global,local}.php',
    ],
    'module_paths' => [
        './module',
        './vendor',
    ],
];


return [
    'modules' => $modules,
    'module_listener_options' => $moduleListenerOptions,
];
