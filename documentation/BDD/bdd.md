# Base de données

## Configuration de la base de donées

Stagetrek propose un container Postgresql permettant de gérer une base de données interne.
Il est cependant possible d'utiliser une base de données issue d'un autre SGBD.

La configuration de l'accés à la base de données se définie par les variables d'environnement suivantes :

```bash
DATABASE_HOST="stagetrek-db.localhost"
DATABASE_NAME="stagetrek"
DATABASE_PORT="5432"
DATABASE_USER="stagetrek_user"
DATABASE_PSWD="ChangeMe!"
```

Vous pouvez accéder à la base de données du container via les commandes suivantes :
```bash
make bash-db
psql -U $POSTGRES_USER -d $POSTGRES_DB -a
```

Le lien avec le modéle de données (via doctrine) est défini dans [data-base.global.php](../../stagetrek/config/autoload/data-base.global.php)

## bddAdmin

La création de la base de données, la mise à jour du modéle ... passent par le module [bddAdmin](../../stagetrek/vendor/unicaen/bddadmin/README.md).


La configuration de BDDAdmin est définie dans [unicaen-bdd-admin.global.php](../../stagetrek/config/autoload/unicaen-bdd-admin.global.php)

Vous pouvez notament y ajouter des connexions vers d'autres serveurs de bases de données (de préférence dans [unicaen-bdd-admin.local.php](../../stagetrek/config/autoload/unicaen-bdd-admin.local.php) )
afin de faire des copie pour des démonstrations, tests ...

Il est important de définir le répertoire contenant les données dans la variable d'environnement `BDD_ADMIN_DIR`
Par défaut, il s'agit du répertoire `data/bdd-admin`

## Actions consoles

Depuis le container core (`make bash`)

- `console bddadmin -h` : Liste les commandes associées
- `console bddadmin:install` : Création de la base de données
- `console bddadmin:update` : Mise à jour de la base de données
  - Cette commande fait une mise à jour des tables, vues, procédures sql, séquences, scripts de migration et des données.
  - Les scripts de migration sont fournis dans [Migration](../../stagetrek/module/BddAdmin/src/Migration).
  - La mise à jour des données est définie dans [Data](../../stagetrek/module/BddAdmin/src/Data).
- `console bddadmin:update-data` : Mise à jour uniquement des données 
- `console bddadmin:update-ddl` : Mise à jour du schéma de la base de données
  - Attention, cette commande remplace le contenu du repetoire [bdd-admin](../../stagetrek/data/bdd-admin)

