# Imports

Stagetrek propose d'importer certaines données et notament la liste des étudiants

# Imports des étudiants

Stagetrek propose 2 méthodes d'imports des étudiants
- Depuis un csv
- Depuis un référentiel exterieur 

## Import depuis un csv

L'import des étudiants par CSV requiert simplement un fichier csv (utilisant le ; en separateur)  contenant les champs suivants :

| Champ                | Valeur attendu            | Obligatoire  | Unique | Description                                    |
|----------------------|---------------------------|--------------|--------|------------------------------------------------|
| Numéro étudiant      | text libre                | true         | true   | Numéro de l'étudiant                           |
| Nom                  | text libre                | true         | false  | Nom de l'étudiant                              |
| Prénom               | text libre                | true         | false  | Prénom de l'étudiant                           |
| Mail                 | adresse mail              | true         | true   | Mail de l'étudiant                             |
| Date de naissance    | Date au format JJ/MM/AAAA | false        | false  | Date de naissance de l'étudiant                |
| Adresse              | text libre                | false        | false  | Adresse de l'étudiant                          |
| Complément d'adresse | text libre                | false        | false  |                                                |
| Code postale         | text libre                | false        | false  |                                                |
| Ville                | text libre                | false        | false  |                                                |
| Cedex                | text libre                | false        | false  |                                                |
| Groupe               | code d'un groupe existant | false        | false  | Code du groupe dans lequel importer l'étudiant |

Le fichier [import_etudiant_exemple.csv](import_etudiant_exemple.csv) est un exemple d'import d'un étudiant

_Note : Un étudiant ne doit être présent qu'une seule fois par fichier d'import. 

Pour l'inscrire dans plusieurs groupe, il faut l'importer plusieurs fois_

## Depuis référentiel étudiants

Stagetrek propose d'importer les étudiants depuis un référentiel exterieur avec 2 "types de sources" possibles :
- Un annuaire LDAP
- Un accés à une base de données externe

### Configuration de l'accés à un annuaire LDAP

L'import depuis un annuaire LDAP requiert d'avoir préalable défini la configuration LDAP
(cf [authentification.md](../Authentification/authentification.md)).

_Note : Seuls les paramètres d'accés à l'annuaire sont requis. L'authentification en tant que tel peut rester via un CAS ou autre_

Dans le cadre de l'import des étudiants, il est important de pouvoir récupérer les données LDAP suivantes :
- eppn
- mail
- displayName
- sn ou surname
- givenName
- supannEtuId
- supannetuetape
- supannEtuAnneeInscription

### Configuration de l'import par une base de données externe

Cette méthode d'import utilise le module [Unicaen/db-import](../../stagetrek/vendor/unicaen/db-import)

L'un des pré-requis est de disposer d'un accés à une base de données (Postgres ou Oracle).
Cette base source doit disposer d'une table (ou d'une vue) (nommée ici par exemple V_ETUDIANTS_STAGETREK) contenant les données suivantes :

| Column     | Type         | Obligatoire  | Unique | Description                                                                     |
|------------|--------------|--------------|--------|---------------------------------------------------------------------------------|
| code       | varchar(64)  | true         | true   | identifiant unique, valeur conseillée : [Code annee]\_[Code VET]\_[Num Etudiant] |
| num_etu    | varchar(10)  | true         | false  | numéro de l'étudiant                                                           |
| nom        | varchar(256) | true         | false  | Nom de l'étudiant                                                               |
| prenom     | varchar(256) | true         | false  | Prénom de l'étudiant                                                            |
| email      | varchar(256) | true         | true   | Adresse mail de l'étudiant                                                      |
| code_annee | varchar(4)   | true         | false  | Année d'inscription de l'étudiant                                               |
| code_vet   | varchar(64)  | true         | false  | Code VET de la promo                                                            |

L'accés au référentiel doit être fourni à l'application via les variables d'environnement suivantes : 

```
REF_ETUDIANT_SOURCE_CODE="apogee"
REF_ETUDIANT_DRIVER="XXXX"
REF_ETUDIANT_DB_HOST="XXXX.XXXX.fr"
REF_ETUDIANT_DB_NAME="XXXX"
REF_ETUDIANT_DB_PORT="5432"
REF_ETUDIANT_DB_USER="stagetrek_user"
REF_ETUDIANT_DB_PSWD="ChangeMe!"
REF_ETUDIANT_TABLE_SOURCE='V_ETUDIANTS_STAGETREK'
```

Les données de cette table doivent être alimentées par une application de scolarité tel que Pégase ou apogée.

_NB : il est recommandé que l'utilisateur `stagetrek_user` n'est accés qu'en lecture seule à la table/vue `V_ETUDIANTS_STAGETREK`

La variable `REF_ETUDIANT_SOURCE_CODE` doit correspondre au code de la source définie via le menu `Administration > Source`. 
Stagetrek propose de base les sources `apogee` et `pegase`.


### Référentiels de promo

Les référentiels de promos (Menu `Administration > Référentiels de promos`) définissent pour chaque source les codes de promo dans lesquels récupérer les étudiants.

Exemple :
- Source : apogée, Code référentiel promo : DFASM1_101 
- Source : ldap, Code référentiel promo : DFASM1_101 


Une fois les référentiels de promos définis, il est nécessaire de les associer aux différents groupe d'étudiants.

### Association groupes et référentiels
Depuis la fiche des groupes, associer les groupes de promos à leurs référentiels correspondant.
C'est par ce lien que les étudiants seront automatiquement inscrit dans les groupes (et aux stages) lors de l'import.

## Import des étudiants depuis un référentiel

Depuis le menu `Etudiants > Etudiants`

1. Importer
2. Onglet `Depuis un Référentiel`
3. Selectionner le référentiel de promo que vous désirez ainsi que l'année d'inscription des étudiants.

Tous les étudiants fournis par le reférentiel selectionné seront créés (ou mis à jour).

Si le référentiel est assiocié à groupe de l'année demandé, les étudiants qui peuvent l'être (c'est à dire n'ayant pas déjà un groupe pour l'année en question)
seront automatiquement inscrit dans ce groupe.

**Cas des imports d'une base de données externe**
Dans le cas d'un import via une base de données externe, il est possible d'importer tous les étudiants de l'année courante via les menus
`Administration > Imports` puis `Administration > Synchro`.
Cette méthode d'import ne permet pas d'intégrer les étudiants dans leur groupe respectif.
Par contre, en cas de problème lors de l'import par le formulaire des étudiants, il possible ici de consulter les logs d'erreurs.

# Autres données
Il est également possible d'importer les données suivantes depuis des fichiers CSV :
- Catégories / Terrains de stages
- Encadrants pédagogiques (Contacts)

Ces procédures requièrent des fichiers csv dont le formatage est fourni dans la documentation fonctionnelle.

