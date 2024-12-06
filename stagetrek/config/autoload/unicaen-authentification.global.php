<?php

/**
 * UnicaenAuthentification Global Configuration
 */
//Mettre par défaut aucune authentification ?
$authService = ($_ENV['AUTH_SERVICE'] && $_ENV['AUTH_SERVICE'] != "") ? $_ENV['AUTH_SERVICE']  : 'db';

$authService = str_replace(' ', '', $authService);
$authService = explode(",", $authService);

$authServicesAllowed = [
    'db' => false,
    'ldap' => false,
    'cas' => false,
    'shib' => false,
];
$formSkiped = sizeof($authService) == 1;
foreach ($authService as $authKey){
    $authServicesAllowed[$authKey] = true;
}

$userpationAllowed = ($_ENV['USURPATION_ALLOWED']) ?? "";
if($userpationAllowed != ""){
    $userpationAllowed = str_replace(' ', '', $userpationAllowed);
    $userpationAllowed = explode(",", $userpationAllowed);
}
else{
    $userpationAllowed=[];
}
//A revoir
$defaultUsers = ($_ENV['DEFAULT_USERS']) ?? "";
if($defaultUsers != ""){
    $defaultUsers = str_replace(' ', '', $defaultUsers);
    $defaultUsers = explode(",", $defaultUsers);
}
else{
    $defaultUsers=[];
}

$config = [
    // Module [Unicaen]Auth
    'unicaen-auth' => [
        /**
         * Flag indiquant si l'utilisateur authenitifié avec succès via l'annuaire LDAP doit
         * être enregistré/mis à jour dans la table des utilisateurs de l'appli.
         *
         */
        'save_ldap_user_in_database' => true,
        'entity_manager_name' => 'doctrine.entitymanager.orm_default', // nom du gestionnaire d'entités à utiliser

        /**
         * Identifiants de connexion LDAP autorisés à faire de l'usurpation d'identité.
         * NB: à réserver exclusivement aux tests.
         */
        'usurpation_allowed_usernames' => $userpationAllowed,
        'default_users' => $defaultUsers,


        'local' => [
            'order' => 1,

            'enabled' => ($authServicesAllowed['ldap'] || $authServicesAllowed['db']),
            'title' => "Connectez-vous",
            'description' => "",
            /**
             * Mode d'authentification à l'aide d'un compte dans la BDD de l'application.
             */
            'db' => [
                'enabled' => ($authServicesAllowed['db']),
            ],
            'ldap' => [
                'enabled' => ($authServicesAllowed['ldap']),
                'username' => "supannaliaslogin",
            ],

            'form_skip' => $formSkiped,
        ],

        /**
         * Paramètres de connexion au serveur CAS :
         * - pour désactiver l'authentification CAS, le tableau 'cas' doit être vide.
         * - pour l'activer, renseigner les paramètres.
         */
        'cas' => [
            'order' => 2,
            'enabled' => ($authServicesAllowed['cas']),
            'description' => "Cliquez sur le bouton ci-dessous pour accéder à l'authentification centralisée.",
            //            Requiere que les autres modes d'authentifications soit desactivé
            'form_skip' => $formSkiped,
        ],

        'shib' => [
            'order' => 3,
            'enabled' => ($authServicesAllowed['shib']),
            'description' =>"Cliquez sur le bouton ci-dessous pour accéder à l'authentification via la fédération d'identité.",
            'logout_url' => '/Shibboleth.sso/Logout?return=', // NB: '?return=' semble obligatoire!
            'form_skip' => $formSkiped,
            /**
             * Alias éventuels des clés renseignées par Shibboleth dans la variable superglobale $_SERVER
             * une fois l'authentification réussie.
             */
            'aliases' => [
//                'eppn'                   => 'HTTP_EPPN',
//                'mail'                   => 'HTTP_MAIL',
//                'eduPersonPrincipalName' => 'HTTP_EPPN',
//                'supannEtuId'            => 'HTTP_SUPANNETUID',
//                'supannEmpId'            => 'HTTP_SUPANNEMPID',
//                'supannCivilite'         => 'HTTP_SUPANNCIVILITE',
//                'displayName'            => 'HTTP_DISPLAYNAME',
//                'sn'                     => 'HTTP_SN',
//                'givenName'              => 'HTTP_GIVENNAME',
            ],

            /**
             * Clés dont la présence sera requise par l'application dans la variable superglobale $_SERVER
             * une fois l'authentification réussie.
             */
            'required_attributes' => [
                'eppn',
                'mail',
                //'eduPersonPrincipalName',
//                'supannCivilite',
                //'displayName',
                //'sn|surname', // i.e. 'sn' ou 'surname'
                //'givenName',
                //'supannEtuId|supannEmpId',
            ],
            /**
             * Configuration de la stratégie d'extraction d'un identifiant utile parmi les données d'authentification
             * shibboleth.
             * Ex: identifiant de l'usager au sein du référentiel établissement, transmis par l'IDP via le supannRefId.
             */
            'shib_user_id_extractor' => [
                'default' => [
                    'supannEmpId' => [
                        'name' => 'supannEmpId',
                    ],
                    'supannEtuId' => [
                        'name' => 'supannEtuId',
                    ],
                ],
            ],
        ],
    ],
];

return $config;
