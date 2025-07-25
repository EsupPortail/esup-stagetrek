#/bin/sh
traiterEvenements(){
 /var/www/html/stagetrek/vendor/bin/laminas evenement:traiter
  return $?
}

traiterEvenements