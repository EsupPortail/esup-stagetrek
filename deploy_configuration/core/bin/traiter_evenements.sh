#/bin/sh
traiterEvenements(){
 /var/www/html/stagetrek/vendor/bin/laminas traiter-evenements
  return $?
}

traiterEvenements