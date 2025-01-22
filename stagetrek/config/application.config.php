<?php

use Application\Provider\Misc\EnvironnementProvider;

$modules = [
    'Laminas\Cache',
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
    'Unicaen\BddAdmin',
    'Application',
    'Console',
    'Evenement',
    'API',
    'Fichier',
    'UnicaenStorage',
];


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();


$applicationEnv = getenv('APPLICATION_ENV') ?: EnvironnementProvider::TEST;
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
