# Sous module de BddAdmin permettant de faire le liens entre BDDAdmin et les données spécifique à l'application

### DDL
!!! le repertoire de DDL `src/DDL` doit être accessible en lecture et en écriture
!!! ne rien mettre manuellement dedans

- Pour récupérer la DDL depuis la base de données : `bddAdmin:update-ddl`.
- Pour mettre à jours la bdd à partir de la ddl : `bddAdmin:update`.

### Data

Exemple pour l'intégration des données : UnicaenUtilisateur

1) faire une Classe `Data/UnicaenUtilisateur`

Cette classe doit contenir une fonction ayant exactement le nom de la table a remplir (les _ inclus) et retourne le tableaux de données
```
...
public function unicaen_utilisateur_user() : array
{
    return [
         [
            "id" => UserProvider::APP_USER_ID,
            "username" => UserProvider::APP_USER,
            "display_name" => "Parcours Accès Santé",
            "email" => "",
            "password" => "app", //Pas de connection possible
            "accessible_exterieur" => false,
            "state" => false,
        ],    
    ];
}
...
```

Une même classe peux fournir plusieurs tables (avec une fonction par table), ce qui peux éviter des classes pour les tables linker par exemple

2) dans update-data.config, mettre la config de mise à jours des données (cf UnicaenBddAdmin)
```
    'unicaen-bddadmin' => [
        'data' => [
            'config'  => [
                'unicaen_utilisateur_user' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'id',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ],
            ],
        ],
    ],
```

3) dans update-data.config, les classes comme sources de données 
```
'unicaen-bddadmin' => [
    'data' => [
        'sources' => [    
            'unicaen_utilisateur_user' => UnicaenUtilisateur::class,
            ...
        ],
    ],
],
```

### Import 

***Note : version actuelle venant de db-import***

Les classes fournisent ici donne la config requise par BddAdmin pour effectuer les imports et les syncro
Elle permettent de simplifier des mises à jours futur ...

Pour Chaque Table dont on veux importer les données, implémenter une Classe `TableXXXImport` et une Classe `TableXXXSynchro`
tel que 

- la fonction `static function getConfig(): array` qui doit retourner la config requise par db-import
- Implémenter la commande console laminas pour faire les imports actions en console
