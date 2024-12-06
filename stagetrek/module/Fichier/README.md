# UnicaenFichier

### Fichier et Nature

Fichier : Entité gérant les informations sur le fichier tel que sont nom, sa taille ...

Nature : Entité permettant de gerer les types de Fichier.
Par défaut, une seul : Document

Quelques routes par défaut permettant les actions de bases :
* `fichier/upload`
* `fichier/download`
* `fichier/archiver`
* `fichier/restaurer`
* `fichier/supprimer`

* Attention, il n'y a pas d'assertion par défaut et les ajout / suppression se font également sur le storage*

### Formulaire d'Upload

Le formulaire d'upload par défaut peux être partiellement configurer, notamment en ce qui concerne les types de fichier attentdu et la taille max.
il est possibles de rajouter ses propres validateur

```
'fichier' => [
        'uplpoad' => [
            'max-size' => '2MB',
            'extentions' => ['pdf', 'csv'], // Extention des fichiers autorisée
            //Pour les csv typeMine = text/plain
            'type-mine' => ['application/pdf', 'text/plain'],
            'validators' => [],
        ],
]
```

### Storage
Dépendance avec UnicaenStorage pour gerer le stockage des fichiers

A définir dans la config
```
'fichier' => [
    ...    
    'storage' => [
        'adapters' => [
                FilesystemStorageAdapter::class => [
                    'root_path' => "/tmp/",
                ],
                ... (conf des autres storage éventuel)
            ],

            /**
             * Adapter de stockage activé (unique).
             */
            'adapter' => FilesystemStorageAdapter::class,
     ]      
    ...    
]
```

Les fonctions `FichierService:create(Fichier $fichier, ?bool $addToStorage=true)` et 
`FichierService:delete(Fichier $fichier, bool $deleteFromStorage=true)` permettent de préciser si l'on souhaite déposer/supprimer le fichier du stockage.

La fonction `FichierService:createFichierFromUpload(array $file, Nature $nature, ?bool $addToStorage=true)`
permet de récupérer le fichier sous forme de tableau (via un formulaire), de creer se fichier et de la déposer dans le storage

### FileNameFormatter

Provider permettant de personnaliser comment sont nommés les fichiers.

2 fonctions a implémentée + 1 héritée : 
* getFileName : nom du fichier
* getFileDir : nom du reper
* getFullName : = getFileDir().'/'. getFileName(); //

* Ces fonciton ne font pas de modification dans l'entité, charge a vous lors de l'ajout de modifier le NomStockage (a priori avec getFullName)*

A définir dans la config
```
'fichier' => [
    ...
    'file-name-formatter' => DefaultFileNameProvider::class
    ...
]
```

2 exemple existe déjà :
* DefaultFileNameProvider : `Ymd-His-uid-NomOriginale`
* NatureBasedFileNameProvider : `CodeNature/Ymd-His-uid-NomOriginale`

