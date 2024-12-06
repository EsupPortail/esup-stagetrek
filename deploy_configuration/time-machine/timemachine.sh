#!/bin/bash

# Nom du fichier de dump
DUMP_FILE="db_backup_$(date +%Y-%m-%d_%H-%M-%S).sql"

echo ${DUMP_FILE}
pg_dump --dbname="postgresql://$DATABASE_USER:$DATABASE_PSWD@$DATABASE_HOST:$DATABASE_PORT/$DATABASE_NAME" > "/timemachine/${DUMP_FILE}"
python3 /upload-dump-database-s3.py /timemachine/${DUMP_FILE} ${DUMP_FILE}