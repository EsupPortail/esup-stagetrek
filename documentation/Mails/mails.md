# Mails

Stagetrek est configuré pour envoyer automatiquement des mails.

Ces envois de mails reposent sur 3 librairies :

- [UnicaenMail](../../stagetrek/vendor/unicaen/mail) pour la gestion des envoies de mails
- [renderer](../../stagetrek/vendor/unicaen/renderer) pour la gestion du corps des mails
- [UnicaenEvenement](../../stagetrek/vendor/unicaen/evenement) pour la gestion d'envoi automatique

## UnicaenMail

UnicaenMail est le module de gestion de l'envoi des mails.
Il est configuré [unicaen-mail.global.php](../../stagetrek/config/autoload/unicaen-mail.global.php)
et repose sur les variables d'environnement `MAIL_`.

### Connection vers le serveur smtp
```bash
MAIL_SMTP_HOST="smtps.local.fr"
MAIL_SMTP_PORT="25"
MAIL_SMTP_TLS="false"
MAIL_SMTP_USE_AUTH="false"
MAIL_SMTP_USERNAME="stagetrek_user"
MAIL_SMTP_PASSWORD="ChangeMe!"
```

A noter que Unicaen mail gére 3 protocoles de communication :
- Non sécurisé (`MAIL_SMTP_PORT=25, MAIL_SMTP_TLS=false, MAIL_SMTP_USE_AUTH=false`)
- TLS : (`MAIL_SMTP_PORT="465", MAIL_SMTP_TLS="true", MAIL_SMTP_USE_AUTH="true"`)
- START_TLS : (`MAIL_SMTP_PORT="587", MAIL_SMTP_TLS="fallse", MAIL_SMTP_USE_AUTH="true"`)

### Gestion des mails
La gestion des mails depuis le menu `Administation > Mails` permet de consulter les logs des mails envoyés, vérifier la configuration, réenvoyer un mails échoués ...

Les 3 variables d'environnement ci-dessous permettent de configurer le Header des mails envoyés :

```bash
MAIL_SUBJECT_PREFIX="StageTrek"
MAIL_FROM_NAME="StageTrek"
MAIL_FROM_EMAIL="ne-pas-repondre@local.fr"
```

Les variables d'environnement suivantes permettent d'effectuer des tests d'en un environnement de pré-production
```bash
MAIL_DO_NOT_SEND="false"
MAIL_REDIRECT="true"
MAIL_REDIRECT_TO="john.doe@fauxmails.fr"
```

## UnicaenRenderer

La gestion du corps des mails envoyés est définie par un système de template configurable depuis le menu `Administration > Gestion des templates`.

Des macros prédéfinies permettent de remplir dynamiquement le contenu des Mails (ie : `VAR[Etudiant#prenom]` pour avoir le prénom de l'étudiant)

## UnicaenEvenement

L'envoi automatique de mails est géré par des événements qui se déclenchent à des dates clés.

La gestion des événements, ainsi que leurs logs sont disponibles via le menu `Administation > Gestion des événements`

Le traitement des événements est géré par un [cron](../../deploy_configuration/core/cron/cron-service) s'executant par défaut toutes les 5 mins

Les logs du traitement des événements sont consultables (pendant 10 jours):
```bash
make bash
nano /var/log/stagetrek/evenements.log 
```
