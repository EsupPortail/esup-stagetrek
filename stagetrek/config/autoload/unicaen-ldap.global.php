<?php

$authService = ($_ENV['AUTH_SERVICE'] && $_ENV['AUTH_SERVICE'] != "") ? $_ENV['AUTH_SERVICE']  : 'db';

$authService = str_replace(' ', '', $authService);
$authService = explode(",", $authService);
$ldapDcName = (isset($_ENV['LDAP_DC_NAME']) && !empty($_ENV['LDAP_DC_NAME'])) ? $_ENV['LDAP_DC_NAME'] : 'unicaen';

foreach ($authService as $authKey){
    $authServicesAllowed[$authKey] = true;
}

if(!isset($authServicesAllowed['ldap'])){
    return [];
}
return [
    // Module [Unicaen]Ldap
    'unicaen-ldap' => [
        'version'               => ($_ENV['LDAP_VERSION']) ?? "",
        'host'                  => ($_ENV['LDAP_REPLICA_HOST']) ?? "",
        'port'                  => ($_ENV['LDAP_REPLICA_PORT']) ?? "",
        'username'              => ($_ENV['LDAP_REPLICA_USERNAME']) ?? "",
        'password'              => ($_ENV['LDAP_REPLICA_PASSWORD']) ?? "",
        'baseDn'                => ($_ENV['LDAP_BASE_DN']) ?? "",
        'bindRequiresDn'        => true,
        'accountFilterFormat'   => (isset($_ENV['LDAP_ACOUNT_FILTER']) && !empty($_ENV['LDAP_ACOUNT_FILTER'])) ? $_ENV['LDAP_ACOUNT_FILTER'] : "(&(objectClass=posixAccount)(supannAliasLogin=%s))",
        'useStartTls'   => false,
    ],

    // Module [Unicaen]App
    'unicaen-app' => [
        'ldap' => [
            'connection' => [
                'default' => [
                    'params' => [
                        'host'                  => ($_ENV['LDAP_REPLICA_HOST']) ?? "",
                        'port'                  => ($_ENV['LDAP_REPLICA_PORT']) ?? "",
                        'username'              => ($_ENV['LDAP_REPLICA_USERNAME']) ?? "",
                        'password'              => ($_ENV['LDAP_REPLICA_PASSWORD']) ?? "",
//                        diff entre les 2 config (Pour unicaen APP on se limite au poeple')
                        'baseDn'                => ($_ENV['LDAP_BRANCH_PEOPLE']) ?? "",
                        'bindRequiresDn'        => true,
                        'accountFilterFormat'   => (isset($_ENV['LDAP_ACOUNT_FILTER']) && !empty($_ENV['LDAP_ACOUNT_FILTER'])) ? $_ENV['LDAP_ACOUNT_FILTER'] : "(&(objectClass=supannPerson)(supannAliasLogin=%s))",
//                        'accountFilterFormat'   => "(&(eduPersonAffiliation=member)(!(eduPersonAffiliation=student))(supannAliasLogin=%s))",
                    ]
                ]
            ],
            'dn' => [
                'UTILISATEURS_BASE_DN'                  => 'ou=people,dc='. $ldapDcName .',dc=fr',
                'UTILISATEURS_DESACTIVES_BASE_DN'       => 'ou=deactivated,dc='.$ldapDcName.',dc=fr',
                'GROUPS_BASE_DN'                        => 'ou=groups,dc='.$ldapDcName.',dc=fr',
                'STRUCTURES_BASE_DN'                    => 'ou=structures,dc='.$ldapDcName.',dc=fr',
            ],
            'filters' => [
                'LOGIN_FILTER'                          => (isset($_ENV['LDAP_LOGIN_FILTER']) && !empty($_ENV['LDAP_LOGIN_FILTER'])) ? $_ENV['LDAP_LOGIN_FILTER'] : '(supannAliasLogin=%s)',
                'UTILISATEUR_STD_FILTER'                => (isset($_ENV['LDAP_UTILISATEUR_STD_FILTER']) && !empty($_ENV['LDAP_UTILISATEUR_STD_FILTER'])) ? $_ENV['LDAP_UTILISATEUR_STD_FILTER'] : '(|(uid=p*)(&(uid=e*)(eduPersonAffiliation=student)))',
                'CN_FILTER'                             => '(cn=%s)',
                'NAME_FILTER'                           => '(cn=%s*)',
                'UID_FILTER'                            => '(uid=%s)',
                'NO_INDIVIDU_FILTER'                    => '(supannEmpId=%08s)',
                'AFFECTATION_FILTER'                    => (isset($_ENV['LDAP_AFFECTATION_FILTER']) && !empty($_ENV['LDAP_AFFECTATION_FILTER'])) ? $_ENV['LDAP_AFFECTATION_FILTER'] : '(&(uid=*)(eduPersonOrgUnitDN=%s))',
                'AFFECTATION_CSTRUCT_FILTER'            => (isset($_ENV['LDAP_AFFECTATION_CSTRUCT_FILTER']) && !empty($_ENV['LDAP_AFFECTATION_CSTRUCT_FILTER'])) ? $_ENV['LDAP_AFFECTATION_CSTRUCT_FILTER'] : '(&(uid=*)(|(ucbnSousStructure=%s;*)(supannAffectation=%s;*)))',
                'LOGIN_OR_NAME_FILTER'                  => '(|(supannAliasLogin=%s)(cn=%s*))',
                'MEMBERSHIP_FILTER'                     => '(memberOf=%s)',
                'AFFECTATION_ORG_UNIT_FILTER'           => '(eduPersonOrgUnitDN=%s)',
                'AFFECTATION_ORG_UNIT_PRIMARY_FILTER'   => '(eduPersonPrimaryOrgUnitDN=%s)',
                'ROLE_FILTER'                           => '(supannRoleEntite=[role={SUPANN}%s][type={SUPANN}%s][code=%s]*)',
                'PROF_STRUCTURE'                        => '(&(eduPersonAffiliation=teacher)(eduPersonOrgUnitDN=%s))',
                'FILTER_STRUCTURE_DN'		            => '(%s)',
                'FILTER_STRUCTURE_CODE_ENTITE'	        => '(supannCodeEntite=%s)',
                'FILTER_STRUCTURE_CODE_ENTITE_PARENT'   => '(supannCodeEntiteParent=%s)',
            ],
        ],
    ],
];