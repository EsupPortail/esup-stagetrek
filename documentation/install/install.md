# INSTALLATION

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

4. Définir les variables d'environnement dans le fichier `.env`

_Reportez-vous aux documentations dédiées pour plus d'informations_ 
- [Configuation](configuation.md)
- [Authentification.md](../Authentification/authentification.md)
- [Authentification](../Authentification/authentification.md)
- [Bdd](../BDD/bdd.md)
- [Import](../Import/import.md)
- [Mails](../Mails/mails.md)
- [Fichier](../Fichier/fichier.md)

5. Build des images docker
```bash
make install
```

6. Création de la base de données
```bash
make bash
console bddadmin:install
```

_A cette étape, vous devriez pouvoir acceder à la page d'accueil de l'application et vous y connecter (si vous n'utilisez pas une authentification locale.)_

7. Création des premiers utilisateurs
```bash
make bash
console utilisateur:create-user
console utilisateur:add-role
```

_Cette étape est nécessaire afin de disposer d'un premier utilisateur en mesure d'attribuer leurs rôles aux autres utilisateurs._

8. Définir les paramètres applicatifs :

Depuis le menu `Administration > Paramètres applicatifs`, définir les paramètres marqués comme `[A définir]`

## Mise à jour de l'application

Lors d'une mise à jours de l'application, executer les commandes suivantes :

```bash
make bash
composer update
console bddadmin:update
```
