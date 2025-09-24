# Authentification

Stagetrek dispose de 4 méthodes d'authentifications possibles :
- Local
- LDAP
- CAS
- Shibboleth

La méthode d'authentifcation utilisée est à définir dans la variable d'environnement `AUTH_SERVICE`
Les valeurs attendues étant respectivement : 'db', 'ldap', 'cas' et 'shib'.

Il est possible d'en utiliser plusieurs : `AUTH_SERVICE='ldap, db'`

D'autres paramètres tels que les messages de connection sont à définir dans [unicaen-authentification.global.php](../../stagetrek/config/autoload/unicaen-authentification.global.php)

Consulter [UnicaenAuthentification](../../stagetrek/vendor/unicaen/authentification/README.md) pour plus d'information.

## Authentification local

L'authentification local permet de gérer des comptes d'utilisateur interne à l'application avec un mot de passe chiffré localement.

Ceux-ci peuvent être créés via le menu `Administration > Gérer les utilisateurs` ou via la console (cf [console.md](../Doc/console.md))

Dans les deux cas, l'utilisateur recevra un lien lui permettant d'initialiser son mot de passe.

## Authentification via un annuaire LDAP

L'authentification via un annuaire LDAP requiert de définir préalablement les variables d'environnement marqué `LDAP_`.

Il est conseillé d'utiliser un compte d'utilisateur n'ayant accés qu'aux données requises à l'identification des étudiants :
- supannaliaslogin 
- eduPersonAffiliation
- displayname
- sn
- givenname
- mail
- dateDeNaissance (optionel)
- supannetuid
- memberOf

D'autres paramètres tels que les filtres LDAP appliqués peuvent être définis dans le fichier [unicaen-ldap.global.php](../../stagetrek/config/autoload/unicaen-ldap.global.php)

## Authentification via un serveur CAS

L'authentification via un serveur CAS requiert de définir les variables d'environnement marqué `CAS_`.


## Authentification via Shibboleth

L'authentification via Shibboleth requiert un enregistrement préalable auprès de Renater.

Les données requises pour l'authentification sont :
- eppn
- mail
- displayName
- sn ou surname
- givenName
- supannEmpId
- supannEtuId 
- supannRefId

Les paramètres d'authentification via Shibboleth tels que les certificats sont à définir dans [shibconf](../../deploy_configuration/shibconf)

# Utilisateurs, Rôles et Priviléges

## Création d'un utilisateur

Dans le cadre d'une authentification LDAP, CAS ou Shibboleth, Stagetrek est conçu pour enregistrer automatiquement les utisateurs lors de leur première connexion.
Pour le cas des comptes locaux, il est nécessaire de le faire manuellement soit via le menu `Administration > Gérer les utilisateurs` ou via la console :

```bash
make bash
console utilisateur:create-user
```

A noter qu'un nouvel utilisateur n'a apriori aucun rôle, et sera uniquement "authentifier" et n'aura aucun droits.

## Rôles et priviléges

La gestion des droits des utilisateurs repose sur un système de Rôles / Priviléges.

Il existe 6 rôles dans Stagetrek :
- Administreur Technique (ie : Membre de la DSI)
- Administreur Fonctionnel (ie : Responsable administratif des stages)
- Etudiant 
- Scolarité (ie : Les gestionnaires de scolarité de l'UFR de médecine)
- Stage et Garde (ie : un professeur hospitalier) 
- Observateur (ie : membre exterieur à l'application qui ne doit avoir accés qu'à des données spécifiques)

Ces rôles sont à attribuer manuellement aux utilisateurs, soit via le menu `Administration > Gérer les utilisateurs`
soit via une action console.
```bash
make bash
console utilisateur:add-role
```

Le rôle étudiant est automatique. Celui-ci est attribué à un utilisateur à partir du moment où son adresse mail correspond à celle définie à un étudiant (`Étudiants > Étudiants`)

La matrice des privilèges (`Administration > Gérer les priviléges`) décrit les actions autorisées aux utilisateurs en fonction de leur rôle.
Par exemple, un étudiant ne pourra accéder qu'aux données qui lui sont propres tandis qu'un administrateur technique pourra modifier les paramètres de l'application.

Lors de l'installation de l'application, il est important de définir un premier utilisateur ayant un rôle administrateur technique via la console.
Cette utilisateur pourra alors attribuer les roles via le menu d'administration. 

Consulter [UnicaenUtilisateur](../../stagetrek/vendor/unicaen/utilisateur/readme.md) et [UnicaenPrivileges](../../stagetrek/vendor/unicaen/privilege/readme.md) pour plus d'informations

## Usurpation
La variable d'environnement `USURPATION_ALLOWED="utilisateur1, utilisateur2"` permet à ces utilisateurs de simuler la connexion en tant qu'un autre utilisateur.
Il est important de ne donner ce droit qu'à des utilisateurs de confiance.

