# Console

Stagetrek propose différentes actions executables en mode console depuis le container :

```bash
make bash
console list
```

A noter que `console` est un alias pour  `/vendor/bin/laminas`

## Principales commandes
Les principales familles de commandes sont :

- `console bddamin` : pour la mise à jour de la base de données (cf [bdd.md](../BDD/bdd.md))
- `console evenement` : pour la gestion des événements (cf [UnicaenEvenement](../../stagetrek/vendor/unicaen/evenement))
- `console utilisateur` : pour la gestion des utilisateurs (cf [authentification.md](../Authentification/authentification.md))
- `console update` : pour mêtre à jour les entités


## Mise à jour des entités

Une mise à jour quotidienne des entités est effectuée par un cron [cron-service](../../deploy_configuration/core/cron/cron-service)

Ces mises à jour consistent principalement à des changements d'états aux dates clés et à la planification d'événements pour les mails automatiques


Les logs sont consultables (pendant 10 jours) :

```bash
make bash
nano /var/log/stagetrek/updates.log 
```

