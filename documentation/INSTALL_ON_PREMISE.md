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

La variable d'environnnement DEFAULT_USERS est a définir lors de l'installation avec le login de l'administrateur initial.
Une fois que vous êtes connecté et vous êtes attribué le role administrateur_technique, 
vous pouvez la réinitialiser en mettant la valeur à "" (DEFAULT_USERS="")

5. Build des images docker
```bash
make install
```

6. Création de la base de données
```bash
make bash
/bin/stagetrek/console bddadmin:update
```

7. Lancer les containers
```bash
make start
```

## TIMEMACHINE
Le service timemachine peux être désactivé au besoin. 
Il s'agit d'un service permettant de faire des dumps de la base de données vers un serveur de sauvegarde.
