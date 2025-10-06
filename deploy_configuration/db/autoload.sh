#!/bin/sh

# Répertoire contenant les scripts SQL à exécuter
sql_scripts_dir="/scripts_to_init/init-db"

# Nom d'utilisateur PostgreSQL
db_user="$POSTGRES_USER"

# Nom de la base de données PostgreSQL
db_name="$POSTGRES_DB"

# Options pour psql
psql_options="-U $db_user -d $db_name -a -v ON_ERROR_STOP=0"

# Boucle pour exécuter tous les fichiers SQL dans le répertoire
for file in "$sql_scripts_dir"/*.sql; do
  echo "Exécution du script SQL : $file"
  psql $psql_options -f "$file"
done