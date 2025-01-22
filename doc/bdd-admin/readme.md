## Liste des commandes 

    $ ./vendor/bin/laminas
Donne la liste des commande

Si update-bdd et update-ddl non présent : il y a un problème de config (pb de path probable)

    $ ./vendor/bin/laminas update-ddl
Créer le fichier ddl de la base 

    $ ./vendor/bin/laminas update-bdd
Utile les fichier ddl pour mettre la base données

!!! creer les tables manquantes, remplace les vue ...
Par défaut ne supprime pas des tables qui n'existerais plus


    $ ./vendor/bin/laminas copy-bdd
Fait une copie d'une base source vers une base destination
(ie : source = prod, destination = preprod)
la destination doit être définie en config




