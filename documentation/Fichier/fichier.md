# Gestions des fichiers

Stagetrek gére l'accés à des fichiers dans le cadre de la gestion des conventions de stage.

Cela passe par les modules [Unicaen/fichier](../../stagetrek/vendor/unicaen/fichier) et [Unicaen/Storage](../../stagetrek/vendor/unicaen/storage)

Deux méthodes de stockage sont proposées (à définir dans la variable d'environnement `STORAGE_TYPE`) :
- local
- S3


## Local
Dans cadre d'un stockage local, il est nécessaire de définir le répertoire de dépot des fichiers

```bash
LOCAL_FILE_STORAGE = "data/fichiers"
```

**Il est important que ce repertoire de dépôts ne soit pas versionné et défini comme un volume dans docker.**


## Protocole S3
La configuration d'accès au stokcage S3 est à définir dans les variables d'environnement `AWS_`.