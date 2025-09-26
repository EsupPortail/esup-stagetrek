
consoleAction(){
  /var/www/html/stagetrek/vendor/bin/laminas $1
  if [ $? != 0 ]; then
    >&2 echo "Erreur lors de l'excution de la commande "$1
    exit $?
  fi
  return $?
}

consoleAction update-contraintes
consoleAction update-annees
consoleAction update-sessions
consoleAction update-affectations
consoleAction update-stages
consoleAction update-ordres-affectations
consoleAction update-etudiants
consoleAction update-preferences
consoleAction update-contacts
consoleAction update-validations-stages
consoleAction update-conventions-stages
consoleAction mail:purge
consoleAction evenement:purger-journal
consoleAction evenement:purger-evenement
consoleAction evenement:generer

