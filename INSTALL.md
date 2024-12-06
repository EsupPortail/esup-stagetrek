# Documentation pour l'installation et la configuration du projet

**Note :** Documentation en cours de réalisation probablement incompléte.

## Clonage du projet

Clonez le projet dans le répertoire `/var/www/html/` :

```bash
git https://github.com/EsupPortail/esup-stagetrek.git /var/www/html/
```


**Note :** À partir de cette étape, toutes les commandes et chemins supposent que vous êtes dans le répertoire
/var/www/html/stagetrek.


## Mise à jour avec Composer

Rendez-vous dans le répertoire du projet et exécutez la commande suivante pour mettre à jour les dépendances :

```bash
composer update
```

**Note :** Attention à la configuration de votre proxy

## Configuration

La configuration à la charge de l'établissement est contenue dans des variables d'environnement a définir dans un fichier `.env`.

La liste des variables d'environnement attendu est disponible dans le fichier `.env_exemple`

Le reste de la configuration peux être être adaptées aux besoins spécifiques de l'établissement, dans les différent fichier de `config/autolod/*.global.php`

Il est possible de creer vos propres config dans des fichier `xxx.local.php` qui surchargeront les configurations global et seront prise en compte dans le .gitignore


## Installation sur le serveur

Lancez la commande suivante pour exécuter le processus d'installation :

```bash
./vendor/bin/laminas bddadmin:install
```

Après l'installation, vérifiez que les données ont été correctement insérées par la mise à jour.

## Création des répertoires nécessaires

Créez les répertoires suivants si ce n'est pas déjà fait :

```bash
mkdir -p ./data/DoctrineORMModule/Proxy
mkdir -p ./upload
```

Assurez-vous que ces répertoires ont les permissions appropriées.


## Configuration de stockage pour les conventions de stages

Deux maniére possible : 
### en local 

```bash
mkdir -p ./data/fichiers/
```
et définir les variables d'environnements
```
STORAGE_TYPE="local"
LOCAL_FILE_STORAGE = "data/fichiers"
```

les conventions seront alors dans repertoire "data/fichiers/conventions/"


### en utilisant S3
Définir les variables d'environnements

```
STORAGE_TYPE="s3"
AWS_ACCESS_KEY_ID= XXXX
AWS_SECRET_ACCESS_KEY= XXXX
AWS_BUCKET_NAME= XXXX
AWS_ENDPOINT= XXXX
AWS_DEFAULT_REGION= XXXX
AWS_VERSION= XXXX
AWS_PROFILE= XXXX
AWS_SHARED_CREDENTIALS_FILE= XXXX
```


## Configuration des paramètres PHP

Vérifiez que les paramètres suivants dans PHP sont correctement configurés à 0 :

    session.cookie_lifetime
    session.gc_maxlifetime

Pour vérifier, consultez la configuration PHP :

php -i | grep session.cookie_lifetime
php -i | grep session.gc_maxlifetime

Modification des paramètres

Si les valeurs ne sont pas correctes, modifiez le fichier /etc/php/8.2/fpm/php.ini et assurez-vous que les lignes
suivantes sont définies :

session.cookie_lifetime = 0
session.gc_maxlifetime = 0

Redémarrez le service PHP-FPM pour appliquer les modifications :

systemctl restart php-fpm


