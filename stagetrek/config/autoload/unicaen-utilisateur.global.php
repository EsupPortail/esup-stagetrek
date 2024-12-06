<?php

use Application\Provider\Roles\IdentityProvider;
use Application\Provider\Roles\UserProvider;
use UnicaenAuthentification\Provider\Identity\Chain;
use UnicaenUtilisateur\Entity\Db\Role;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Provider\Role\DbRole;
use UnicaenUtilisateur\Provider\Role\Username;
use UnicaenUtilisateur\Service\User\UserService;
use UnicaenUtilisateurLdapAdapter\Service\LdapService;

$rechercheIndividuService = [
    'app' => UserService::class,
];

//Rajout du LDAP uniquement si la configuration est défine
//TODO : rajouter un service a configué en variable d'environnement pour utiliser un référentiel spécifique a chaque université
// (Appel a un webService ?)
if(isset($_ENV['LDAP_REPLICA_HOST']) && $_ENV['LDAP_REPLICA_HOST'] != ""){
    $rechercheIndividuService['ldap'] =  LdapService::class;
}

return [
    /**
     * L'entité associée aux utilisateurs peut être spécifiée via la clef de configuration ['zfcuser']['user_entity_class']
     * Si elle est manquante alors la classe @see \UnicaenUtilisateur\Entity\Db\User est utilisée
     * NB: la classe spécifiée doit hériter de @see \UnicaenUtilisateur\Entity\Db\AbstractUser
     */
    'zfcuser' => [
        'user_entity_class' => User::class,

        /**
         * Enable registration
         * Allows users to register through the website.
         * Accepted values: boolean true or false
         */
        'enable_registration' => false,
    ],

    'bjyauthorize' => [
        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         *
         * for ZfcUser, this will be your default identity provider
         */
        'identity_provider' => Chain::class,

        /* role providers simply provide a list of roles that should be inserted
         * into the Laminas\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Laminas\Db adapter.
         */
        'role_providers' => [
            /**
             * Fournit les rôles issus de la base de données éventuelle de l'appli.
             * NB: si le rôle par défaut 'guest' est fourni ici, il ne sera pas ajouté en double dans les ACL.
             * NB: si la connexion à la base échoue, ce n'est pas bloquant!
             */
            DbRole::class => [],

            /**
             * Fournit le rôle correspondant à l'identifiant de connexion de l'utilisateur.
             * Cela est utile lorsque l'on veut gérer les habilitations d'un utilisateur unique
             * sur des ressources.
             */
            Username::class => [],
        ],
    ],

    'unicaen-auth' => [
        /**
         * L'entité associée aux roles peut être spécifiée via la clef de configuration ['unicaen_auth']['role_entity_class']
         * Si elle est manquante alors la classe @see \UnicaenUtilisateur\Entity\Db\Role est utilisée
         * NB: la classe spécifiée doit hériter de @see \UnicaenUtilisateur\Entity\Db\AbstractRole
         */
        'role_entity_class' => Role::class,
    ],

    'unicaen-utilisateur' => [
        /**
         * Liste des services utilisés pour rechercher un utilisateur existant ou à ajouter dans l'application
         */
        'recherche-individu' => $rechercheIndividuService,
        'identity-provider' => [ //Requis ici pour avoir les roles automatiques
            IdentityProvider::class,  // Applicatifs
        ],
        'default-user' => UserProvider::APP_USER_ID,
    ],
];