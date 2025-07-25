## BDD-Admin

La mise à jour de la base de données passe par le module Unicaen/bdd-admin

## Usage 

Depuis le container core (`make bash`)

- Listes les commandes possible : `/bin/console bddadmin:`
- Mise à jour de la base de données `/bin/console bddadmin:update`
Cette commande fait une mise à jour des tables, vues, procédures sql, séquences, scripts de migration et des données 
Les scripts de migration sont fournis dans [Migration](../../stagetrek/module/BddAdmin/src/Migration)
La mise à jour des données est définie dans [Migration](../../stagetrek/module/BddAdmin/src/Data)

- Mise à jour uniquement des données `/bin/console bddadmin:update-data`

## DDL de la base de données :

Les schémas DDL de la base de données sont définis dans data/bdd-admin/DDL
Ils sont mis à jour via la commande `/bin/console bddadmin:update-ddl`
