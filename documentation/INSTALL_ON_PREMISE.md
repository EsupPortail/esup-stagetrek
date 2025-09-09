# INSTALLATION [WIP]

## Prérequis
- [Docker](https://docs.docker.com/get-docker/)
- [Docker-compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

## Composition de la stack
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

## Commandes utiles

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

### Dans le(s) containers

```bash
# sur container apache/core (make bash)


```

## TIMEMACHINE
Le service timemachine peux être désactivé au besoin. 
Il s'agit d'un service permettant de faire des dumps de la base de données vers un serveur de sauvegarde.
