# Paramétrage et configuration Stagetrek

Stagetrek requiert plusieurs niveaux de configuration. 

Certains paramètres sont à définir lors de l'installation via des variables d'environnement dans un fichier `.env`.

**!!! Ce fichier ne doit jamais être publié sur dans un dépôt git**

La majorité de la configuration est définie dans repertoire [autoload](../../stagetrek/config/autoload)

Chaque fichier de configuration "global" peut être surchagé par un fichier de configuration "local" (ie : `unicaen-authentification.local.php`)

Nous donnerons ici les variables d'environnement à définir, même s'il est possible de ne pas les utiliser en remplacant leurs usages dans les différents fichiers de configuration.

## Variables d'environnement

Consulter le fichier [.env.example](../../.env.example) pour la liste des variables d'environnement à définir.

### Configuration du serveur

```bash
STAGETREK_INSTANCE_URL='https://stagetrek.local.fr'
URI_HOST="stagetrek.local.fr:8443"
URI_SCHEMA="https"
```

Si votre réseau requiert des paramètres de proxy, ceux-ci peuvent être définis dans les variables d'environnement "Proxy" :
```bash
PROXY_HOST="proxy.XXXXX.fr"
PROXY_PORT="3128"
PROXY_URL="http://proxy.XXXXX.fr:3128"
```

### Environnement applicatif et maintenance

Il est possible de modifier le type d'environnement applicatif via les variables d'environnement suivantes :
```bash
APP_ENV="production"
CONSOLE_ENV="production"
```

Les valeurs attendues sont 
- "production" (valeur par défaut)
- "development"
- "test"
- "demo"

Le mode maintenance est activable via les variables suivantes :
```
MAINTENANCE_ACTIVE="false"
MAINTENANCE_CLI_ACTIVE="false"
```

Le message d'information en mode maintenance est à définir dans le fichier [stagetrek.global.php](../../stagetrek/config/autoload/stagetrek.global.php)
à la clé ['unicaen-app']['maintenance']


### Autres variables d'environnement:
La description des autres variables d'environnement sont disponibles dans leurs documentations dédiées.


## Paramètrages applicatifs

Certains paramètres de l'application sont modifiables en back office depuis le menu `Administration > Paramètres applicatifs`

Parmi ces paramètres, certains possédent des valeurs par défaut, d'autres (marqué [A renseigner]) requiert une initilisation lors de l'installation :
- Pied de page 
- Convention de stage


