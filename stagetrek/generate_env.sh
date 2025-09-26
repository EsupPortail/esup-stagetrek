ENV_FILE=/var/www/html/stagetrek/.env
ENV_FILE_EXAMPLE=/var/www/html/stagetrek/.env_example

#rm $ENV_FILE 

# Run only if .env not exists
# For kubernetes, ignored by .gitignore
cat $ENV_FILE_EXAMPLE | while read ligne ; do
  echo ${ligne}=$(eval "echo \$$ligne") >> $ENV_FILE
done
/usr/local/bin/docker-php-entrypoint php-fpm