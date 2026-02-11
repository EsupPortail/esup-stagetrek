CHANGELOG
=========
1.8.1 (11/02/2026)
------------------
- [Bug Fixe] : recalcul du nombre de places affectés après l'execution de l'algorithme

1.8.0 (03/02/2026)
------------------
- [Update / Bug Fixe] Passage du core en Debian 13
  - Correction du problème d'envoie des mails avec la nouvelle chaîne de certification Harica
- [A prévoir] Montée de version des autres images

1.7.7 (07/01/2026)
------------------
- [Bug Fixe] : Suppresion des sessions de stages en même temps que le groupe
- [Bug Fixe] : Import manquant
- Clé étrangére manquante pour la validation des stages

1.7.6 (07/01/2026)
------------------
- [Secu] : Update de la lib AWS
- [Bug Fixe] : non envoie des événements de validation des stages

1.7.5 (10/12/2025)
------------------
- [Bug Fixe] : import manquant lors du retrait d'un étudiant
- [Bug Fixe] : Correction sur la séquence de UnicaenUtilisateur
- [Bug Fixe] : crontab et logrotate
- [New] - création d'un evenement de test permettant de vérifier le fonctionnement des crons
- [New] - variables d'environnement EVENEMENTS_SIMULATE_EXECUTION qui permet en pré-prod de ne pas traiter les événements (ou au contraire de les activer pour des tests)

1.7.4 (09/12/2025)
------------------
- [Secu] : Possibilité d'interdire des rôles par filtrage IP
- Autre modif mineure

1.7.3 (01/12/2025) 
------------------
- [Correctif mineur] : Relecture de certaines pages

1.7.2 (28/11/2025) [En test]
------------------
- [Bug fixe] import : correctif mineur sur les valeurs par défaut, synthaxe du HEADER
- [Bug fixe] import : suppression du caractére spécial mis automatiquement par excel en début de fichier
- [Bug fixe] page étudiant : suppression du Point médiant en JS

1.7.1 (27/11/2025) [En test]
------------------
- [Bug fixe] mail d'affectation a des contacts qui ne sont pas liée a un terrain
- Action pour lancer le recalcul des ordres d'affectations

1.7.0 (30/10/2025) [En test]
------------------
- Contact des terrains :
  - nouvelle propriété : send_mail_auto_liste_etudiants_stage pour définir si le contacts doit recevoir la liste des étudiants affectées
  - import : nouveau champs de le csv "liste-etudiants" pour définir cet attribue

-  Envoie manuel des liens de validation : on ne passe plus par les événements, mais directement par UnicaenMail
  - On ne renvoie bien qu'un seul token, pour un stage bien précis à 1 et un seul responsable de stage bien précis

- Envoie automatiques des liens de validation :
  - Les valideurs recoivent 1 seul mail par session contenant tout les stages (et les tokens) qu'ils doivent valider
  - 1 seul événement gérant l'envoie de tout les mails (à vérifier en cas de bug sur l'un des mails si celà ne poserais pas des problèmes)
  - Reste toujours la possibilité de faire un envoie 1 à 1 pour les responsable de stage n'ayant pas recu certains mails
  - Liste des mails envoyés aux différents contacts
  - Ne réenvoie pas le mails si le mails a déjà été envoyées. Permet en cas d'arrêt en cours de l'événement de ne pas réenvoyer les mails aux contacts qui l'on déjà recu.

- Modification manuel d'une affectation
  - si case coché : mail prévenant l'étudiant 
  - si case coché : mails prévenant les contacts du terrain de la modification de la liste des étudiants qu'ils encadrent

- Modification multiples d'affectations
  - Pour les affectation passant dans l'état "Validée" envoie d'un mail a l'étudiant et d'un mail par contact avec la listes des étudiants qui leurs sont affectés.

1.6.0 (29/10/2025) [En test]
------------------
- Les stages peuvent être découpé en multiples périodes (alternance Stage/cours par exemple)
- Tag pour les sessions de rattrapages

1.5.1 (28/10/2025)
------------------
- Import : précisions des champs obligatoires

1.5.0 (28/10/2025)
------------------
- Mise en place de UnicaenIndicateur

1.4.4 (30/09/2025)
------------------
- [Bug Fixe] : mail auto qui n'utilisait pas la conf tls

1.4.3 (30/09/2025)
------------------
- [UnicaenEvenement] : 6.1.0 - annulation d'événement trop vieux
- Nouvelle variable d'environnement optionnel : EVENEMENTS_DELAI_PEREMPTION
- EVENEMENTS_MAX_TIME_EXECUTION : valeur en DateInterval et non plus en seconde
- BugFixe : label des liens supprimées

1.4.2 (26/09/2025)
------------------n
- [UnicaenTag] : mise en place de UnicaenTag
- [Bug fixe] : Cron et logrotate  + autre fixe mineur

1.4.1 (25/09/2025)
------------------
- Mise à jours automatiques des états des événements
- [Bug fixe] Planification des mails lors de la validations des affectations de stages y compris a postériori

1.4.0 (24/09/2025)
------------------
- [Documentations] : Mise au propre de la documentation
- [Bug fixe] : Timezone des containers
- [Référentiel] : refonte de l'import des étudiants via un référentiel externe
  - Consulter la documentation correspondante
  - Référentiels gérés si les données de configurations sont fournis :
    - annuaire LDAP (requiert une configuration LDAP à fournir pour le mode SaaS)
    - Une BDD externe :(requiert une configuration spécifique à fournir pour le mode SaaS)
  - Import par CSV : ajout du code des groupes
  - Champs code pour les Entités Années, Etudiant et groupe
  - Association entre les sources de référence et les groupe d'étudiant
- [Utilisateur] : création via la console 
  - Consulter la documentation correspondante
- [Bug fixe] : création manuelle d'évenement
- [UnicaenTag] : Ajout de la librairie UnicaenTag
  - Début d'usage pour les années universitaire

1.3.6 (11/09/2025)
------------------
- [Bug fixe] : retrait d'un étudiants lorsqu'il a déjà des stages de planifiés
- [Bug fixe] : calcul des états lors de l'ajout/le retrait d'un étudiants dans un groupe

1.3.5 (09/09/2025)
------------------
- [Bug fixe] : Correctif permettant d'envoyer des mails via un autre protocole que TLS
- [Bug fixe temporaire] : Ignore la config Shib 'required_attributes' qui pose problème avec le supannEmpId|supannEtuId
TODO : erreur à investiguer

1.3.2 (28/08/2025)
------------------
- script bin/stagetrek/console manquant
- Schema de bdd manquant
- [Bug fixe] Modificaitons des dates de naissances des étudiants


1.3.1 (24/07/2025)  
------------------
- Config pour voir les messages d'erreur en mode dev
- Upgrade des librairies
- Nettoyage des fichiers de deploy_config

1.3.0 (23/07/2025)
------------------
- Nettoyage de bdd admin
- Ajout d'un code sur faq_categorie_question
- Passage à APP_ENV pour éviter les doublons/conflit avec APPLICATION_ENV
- Passage à 10 déciles sur les niveau de demande de terrains de stage
- Tri des étudiants par Nom dans les pages des sessions de stages

1.2.21 (01/07/2025)
------------------
- [Bug Fix] : import des étudiants : pb si l'on ne fournissait pas la date et l'adresse

1.2.20 (06/06/2025)
------------------
- [Bug Fix] : import des étudiants : pb d'accents dans les entêtes
- [Update] css pour le menu

1.2.19 (22/05/2025)
------------------
- [Sécu] : restriction d'accès au répertoire public

1.2.16 (15/05/2025)
------------------
- [Bug fix] : Vérification des codes de terrains et catégories lors de l'import
- Import avec un code vide possible pour utiliser la génération aléatoire
- Import avec un code défini manuellement possible

1.2.15 (13/05/2025)
------------------
- [Bug fix] : Action console du traitement des événements

1.2.14 (06/05/2025)
------------------
- [Bug fix] : problème pour l'affichage des codes des terrains et des catégories (Cf. modif ci-dessous)
- Les codes des catégories et des terrains sont toujours visibles
- Fiche des catégories de stage pour pouvoir accéder à leurs codes (plus d'autres infos)
- Possibilité de définir/modifier le code des terrains et des catégories de stages

1.2.11 (29/04/2025)
------------------
- Capture des dernières exceptions avec code d'erreur 500
- Dépendance entre stagetrek-core et stagetrek-service/stagetrek-db

1.2.10 (16/04/2025)
------------------
- [Bug fix] sur les envois de mails
- Blocage des versions des librairies utilisées

1.2.9 (10/04/2025)
------------------
- [Bug fix] de la ddl pour les créations d'utilisateurs


1.2.5 (09/04/2025)
------------------
- [Bug fix] si la conf LDAP n'est pas fournie

1.2.4 (01/01/2025)
------------------
- Harmonisation de certaines constantes
- [Update] de unicaenUtilisateur
- Divers

1.2.3 (24/02/2025)
------------------
- [Bug fix] : événement considéré comme non généré car mauvais paramètres


1.2.2 (10/02/2025)
------------------
- Transformation en vrai module Unicaen de Fichier et Storage

1.2.1 (28/01/2025)
------------------
- [Bug fix] Envoi des mails à de multiples destinataires : nouvelle signature de la fonction sendMail de UnicaenMail
- [update] Quelques maj de conf pour l'image

1.2.0 (22/01/2025)
------------------
- Déploiement d'une release 1.2.0
- [Update] du gitlab-ci pour le déploiement automatique vers EsupPortail/esup-stagetrek

1.0.34 (21/01/2025)
------------------
- [Bug fix] export des affectations

1.0.33 (17/01/2025)
------------------
- [Fix de sécu] : passage à tinymce 7.6.0
- [Update] : passage à unicaen-renderer 7.0.3
- Début de réorganisation des fichiers css/js dans public/vendor

1.0.33 (14/01/2025)
------------------
- [Update] Passage aux versions des 7.x des lib unicaen
- [Bug fix] : contact donc le nom apparaissait mal 

=========
1.0.32 (13/01/2025)
------------------
- Affichage des terrains affectés sur les différentes listes des fiches d'une session de stages
- [Bug fix] : suppression d'un fichier qui n'existe pas n'est pas bloquant
- [Bug fix] : Génération manuel des liens pour les contacts possibles même en dehors des phases (pour pouvoir gérer des cas particuliers)
- [Update de sécu] : mise en place des CSRF pour les formulaires de recherche

1.0.31 (12/12/2024)
------------------
- [Bug fix] conf ldap

1.0.29 (10/12/2024)
------------------
- Passage a UnicaenAuth/Utillisateur 6.3
- [Bug fix] sur les pop over
- Nettoyage de relique dans les fonctions JS inutile
- Nettoyage de fichier de conf 

1.0.28 (06/12/2024)
------------------
[Fix de sécu] 
- Lib JS moment mise à jour
[Update]
- UnicaenFichier et UnicaenStorage pour les conventions de stage
- Passage à UnicaenEtat pour la validation des états
- Séparation dans la page de sessions des onglets préférences, conventions et validation

1.0.27 (25/11/2024)
------------------
[Fix]
- Correctif sur les préférences satisfaites affichées

[Update]
- Paramétrage du pied de page
- Début de passage a UnicaenEtat pour la validation des stages (non visible en back-end)


1.0.26 (21/11/2024)
------------------
- [Fix] Bug majeur : Session de stages et création / suppressions des stages
- [Update] Possibilité de laisser des commentaires cachés lors de la validation du stage


1.0.25 (19/11/2024)
------------------
- [Fix] Conf pour le SMTP
- [Update] Ajout/modification/suppression des sources de données

1.0.24 (19/11/2024)
------------------
[Fix de sécu]
- Faille potentielle de sécurité avec Shibb

[Update]
- BDD-Admin et autres petits correctifs


1.0.20 (18/11/2024)
------------------
[Update]
- Imports des étudiants par API
- Suppression définitive des terrains de stage dit associés. Remplacer par les terrains tagués "secondaire"
- Suppression définitive des statuts, remplacé par la librairie UnicaenEtat
- Imports des étudiants en CSV : harmonisation avec les autres méthodes d'import, suppression des contrôles de données avec les résultats du LDAP
- Suppression des systèmes d'alerte. Le contrôle des données se fait si nécessaire lors de la maj des états
- A revoir : Système d'alerte pour des données non critiques (voir si nécessaire car en place dans les versions précédentes mais jamais réellement utilisé/lu).

1.0.19 (13/11/2024)
------------------
[Mise à jour]
- Passage complet à BDD-Admin
- Remplacement des statuts par les états
- Suppression des alertes (A revoir la gestion complète)
- Début de suppression des tables obsolètes (annuaire ...)
- Nettoyage des mails automatiques

[Fix]
- BDD-admin séquence manquante

1.0.13 (24/10/2024)
------------------
- Passage a ObjectManager

1.0.12 (16/10/2024)
------------------
- UnicaenEtat pour les années
- Changement de calcul des ordres d'affectation
- Début d'UnicaenEtat pour les étudiants
- 
1.0.11 (14/10/2024)
------------------
- Modification des procédures de recalcul des ordres d'affectation
- Début de passage à UnicaenEtat

1.0.9 (08/10/2024)
------------------
- V2 de l'algorithme d'affectation

1.0.8 (08/10/2024)
------------------
- Mise en place de BDD admin
- Refonte de la gestion User <-> Etudiant


1.0.7 (04/10/2024)
------------------
- Préparation pour le mode SAaS
