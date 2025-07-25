#!/bin/sh
#TODO / bestPractice : mettre les chemins en variables d'environnement
#TODO : réfléchir isi l'on doit creer c'est repertoire ici ou dans le dockerfile
#ici = execution a chaque lancement de l'image, dans le dockerFile = uniquement a la construction de l'image
#Création des répertoires requis par l'application
echo "Créations des repertoires de données"
mkdir -p data

echo "Créations de cache"
mkdir -p data/cache
mkdir -p data/DoctrineModule/cache
mkdir -p data/DoctrineORMModule/Proxy
chmod -R 777 data/cache
chmod -R 777 data/DoctrineModule/cache
chmod -R 777 data/DoctrineORMModule/Proxy
#Clear des fichier de les répertoire de cache
rm -rf data/DoctrineModule/cache/*
rm -rf data/DoctrineORMModule/Proxy/*

echo "Créations des repertoires de conventions"
mkdir -p ${CONVENTIONS_DIRECTORY}
chmod -R 777 ${CONVENTIONS_DIRECTORY}

#S'il n'existe pas (lors de la premiére install)
echo "Créations des repertoires pour db-admin"
mkdir -p ${BDD_ADMIN_DIR}
chmod -R 777 ${BDD_ADMIN_DIR}
echo "Composer"
composer install

echo "Créations du repertoires de log"
mkdir -p /var/log/stagetrek
chmod -R 777 /var/log/stagetrek

echo "Lancement du supervisor"
/usr/bin/supervisord -n -c /supervisord.conf
