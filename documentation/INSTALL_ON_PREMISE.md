# INSTALLATION [WIP]

## Prérequis
- [Docker](https://docs.docker.com/get-docker/)
- [Docker-compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

Selon le système d'exploitation, l'installation de make devra être faite séparément de Docker

```bash
sudo apt update
sudo apt -y install make
```

## Composition de la stack

- PHP 8.2
- PGSQL 17.4

### 4 containers docker
- **BDD**: PostgreSQL 17.4 Alpine
- **PHP**: PHP 8.2 FPM bullseye
- **APACHE**: Ubuntu 20.04
- **TIMEMACHINE**: debian:bookworm-20240311 
- **MEMCACHED**: memcached:1.6.21

## Installation

1. Cloner le projet
```bash
git clone https://github.com/EsupPortail/esup-stagetrek.git
```
2. Se déplacer dans le dossier du projet
```bash
cd StageTrek
```
3. Créer un fichier `.env` à la racine du projet
```bash
cp .env.example .env
```

4. Modifier les variables d'environnement dans le fichier `.env`

Pensez notamment à y incure la configuration vers votre système d'authentification ...

Si aucune information n'est renseignée pour la base de données, le container de BDD sera utilisé. Il est préférable d'utiliser un SGBD installé sur une autre machine.
L'application utilise une BDD PostgreSQL 17.

La variable d'environnnement DEFAULT_USERS est a définir lors de l'installation avec le login de l'administrateur initial.
Une fois que vous êtes connecté et vous êtes attribué le role administrateur_technique, 
vous pouvez la réinitialiser en mettant la valeur à "" (DEFAULT_USERS="")

4. bis Mettre à jour les librairies des l'application

Avant de monter les images/containers, il est conseiller de mettre à jour les librairies utilisées par l'application.

```bash
cd /path/to/install/stagetrek
sudo -u www-data composer update # remplacer www-data par l'utilisateur propriétaire des fichiers. Eviter root
```

5. Build des images docker
```bash
make install
```

6. Création de la base de données
```bash
# la commande se lance via le container core / apache
make bash
/bin/stagetrek/console bddadmin:install
# si /bin/stagetrek/console n'est pas disponible on peut utiliser /var/www/html/stagetrek/vendor/bin/laminas à la place
```

6. bis. Mise à jour de la base de données / import des données depuis le DDL
```bash
make bash
/bin/stagetrek/console bddadmin:update
```

7. Lancer les containers
```bash
make start
```

## Commandes utiles et emplacements

### Sur la VM

```bash
make help                 # Donne la liste des commandes ci-dessous
make install              # Build des conteneurs de l'application
make uninstall            # Désinstallation
make start                # Démarre les conteneurs de l'application
make stop                 # Stoppe les conteneurs de l'application
make bash                 # Entrer dans le bash du container php
make bash-db              # Entrer dans le container de la base de données
make bash-service         # Entrer dans le container Apache
make logs                 # Afficher les logs des containers docker
make rebuild              # Reconstruit tous les conteneurs
make clean                # Vide les caches Docker ## Attention : Supprime tous les volumes, containers, images et network inactifs.
```
Les fichiers de configuration de l'application sont disponibles ici :

```bash
/path/to/repo/stagetrek/stagetrek/config/autoload
xxx@xxx-t24:/usr/local/applis/stagetrek/html/stagetrek/stagetrek/config/autoload# ll
total 76
-rwxrwxrwx 1 www-data www-data  773 22 août  10:43 api.global.php
-rwxrwxrwx 1 www-data www-data  986 22 août  10:43 cas.global.php
-rwxrwxrwx 1 www-data www-data  632 22 août  10:43 console-cli.global.php
-rwxrwxrwx 1 www-data www-data  735 22 août  10:43 data-base.global.php
-rwxrwxrwx 1 www-data www-data 4099 28 août  14:34 stagetrek.global.php
-rwxrwxrwx 1 www-data www-data 5530 22 août  10:43 unicaen-authentification.global.php
-rwxrwxrwx 1 www-data www-data 3116  1 sept. 14:57 unicaen-bdd-admin.global.php
-rwxrwxrwx 1 www-data www-data  543 22 août  10:43 unicaen-code.global.php
-rwxrwxrwx 1 www-data www-data  257 22 août  10:43 unicaen-evenement.global.php
-rwxrwxrwx 1 www-data www-data 2081 22 août  10:43 unicaen-fichier.global.php
-rwxrwxrwx 1 www-data www-data 4072 22 août  10:43 unicaen-ldap.global.php
-rwxrwxrwx 1 www-data www-data 2074 22 août  10:43 unicaen-mail.global.php
-rwxrwxrwx 1 www-data www-data 1491 22 août  10:43 unicaen-privilege.global.php
-rwxrwxrwx 1 www-data www-data 3737 22 août  10:43 unicaen-utilisateur.global.php
-rwxrwxrwx 1 www-data www-data  176 22 août  10:43 version.global.php
-rwxrwxrwx 1 www-data www-data 5517 22 août  10:43 zenddevelopertools.global.php
```

### Dans le(s) containers

```bash
# sur container apache/core (make bash)
# obtenir les commandes possible de bddadmin
root@7f565369b680:/var/www/html/stagetrek# /bin/stagetrek/console bddadmin:      
      bddadmin:install          Peuple la base de données à partir de la DDL et du jeu de données par défaut         
      bddadmin:install-demo     Peuple la base de données à partir d'un jeu de démo                                  
      bddadmin:update           Met à jour la base de données & ses données à partir de la DDL et du jeu de données  
      bddadmin:clear            Vide la base de données                                                              
      bddadmin:update-ddl       Met à jour la DDL à partir des définitions de la base de données                     
      bddadmin:update-data      Met à jour les données de la base de données                                         
      bddadmin:update-sequences Mise à jour des séquences de la base de données                                      
      bddadmin:copy-to          Copie vers une autre BDD                                                             
      bddadmin:copy-from        Copie depuis une autre BDD                                                           
      bddadmin:load             Chargement de la base de données depuis un fichier                                   
      bddadmin:save             Sauvegarde de la base de données dans un fichier                                     
      bddadmin:test-migration   Test de script de migration args: <Nom partiel du script> <utile|before|after>.                       
```

## TIMEMACHINE
Le service timemachine peux être désactivé au besoin. 
Il s'agit d'un service permettant de faire des dumps de la base de données vers un serveur de sauvegarde.
