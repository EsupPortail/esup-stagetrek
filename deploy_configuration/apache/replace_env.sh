#!/bin/bash

#TODO : a vérifier : faire uniquement envsubst afin de ne pas avoir a lister ici les variables d'environnement que l'on veut remplacer (et qui peuvent être amener a changer : risque d'oublie)
#!!! a voir quand il doit être utilisé, et surtout comprendre pourquoi les fichier de conf sont monté en tant que volume
#envsubst '${STAGETREK_INSTANCE_URL}' < /template-000-default.conf  > /etc/apache2/sites-available/000-default.conf
#envsubst '${STAGETREK_CORE_POD_NAME} ${STAGETREK_INSTANCE_URL} ${APPLICATION_ENV}' < /template-default-ssl.conf > /etc/apache2/sites-available/default-ssl.conf

envsubst < /template-000-default.conf  > /etc/apache2/sites-available/000-default.conf
envsubst < /template-default-ssl.conf > /etc/apache2/sites-available/default-ssl.conf
