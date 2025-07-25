#!/bin/sh

envsubst < /template-000-default.conf  > /etc/apache2/sites-available/000-default.conf
envsubst < /template-default-ssl.conf > /etc/apache2/sites-available/default-ssl.conf
