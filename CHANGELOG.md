CHANGELOG
=========
1.0.34 (21/01/2025)
------------------
- [Bug fix] export des affectations

1.0.33 (17/01/2025)
------------------
- [Fix de sécu] : passage a tinymce 7.6.0
- [Update] : passage a unicaen-renderer 7.0.3
- Début de réorganisation des fichiers css/js dans public/vendor

1.0.33 (14/01/2025)
------------------
- [Update] Passage aux versions des 7.x des lib unicaen
- [TODO] Bug non corrigé : Header/footer des conventions de stages
- [TODO] Versions de TinyMce à modifier
- [Bug fix] : contact donc le nom apparaissait mal 

=========
1.0.32 (13/01/2025)
------------------
- Affichage des terrains affectés sur le différente listes des fiches d'une sessions de stages
- [Bug fix] : suppression d'un fichier qui n'existe pas n'est pas bloquant
- [Bug fix]  : Génération manuel des liens pour les contacts possible même en dehors des phases (pour pouvoir gerer des cas particulier)
- [Maj de sécu] : mise ne place des CSRF pour les formulaires de recherche

1.0.31 (12/12/2024)
------------------
- Bug fix conf ldap

1.0.29 (10/12/2024)
------------------
- Passage a UnicaenAuth/Utillisateur 6.3
- Correctifs sur les popover
- Nettoyage de relique dans les fonctions JS inutile
- Nettoyage de fichier de conf 

1.0.28 (06/12/2024)
------------------
[Fix de sécu]
- Lib JS moment mise à jours
[Mise à jours]
- UnicaenFichier et UnicaenStorage pour les conventions de stage
- Passage a UnicaenEtat pour la validation des états
- Séparation dans la page de sessions des onglets préférences, conventions et validation

1.0.27 (25/11/2024)
------------------
[Fix]
- Correctif sur les préférences satisfaite affichée

[Mise à jours]
- Paramétrages du pieds de pages
- Début de passage a UnicaenEtat pour la validation des stages (non visible en back-end)


1.0.26 (21/11/2024)
------------------
[Fix]
- Bug majeur : Session de stages et création / suppressions des stages

[Mise à jours]
- Possibilité de laisser des commentaires caché lors de la validation du stage


1.0.25 (19/11/2024)
------------------
[Fix]
- Conf pour le SMTP
[Mise à jours]
- Ajout/modification/suppression des sources de données

1.0.24 (19/11/2024)
------------------

[FIX de sécurité]
- Faille potentiel de sécurité avec Shibb

[Mise à jours]
- BDD-Admin et autres petit correctif


1.0.20 (18/11/2024)
------------------

[Mise à jours]
- Imports des Etudiants par API
- Suppression définitive des terrains de stage dit associée. Remplacer par les terrains tagué secondaire
- Suppression définitive des statuts, remplacé par la librairie UnicaenEtat
- Imports des étudiants en CSV : harmonisation avec les autres méthodes d'import, suppression des controles de données avec les résultat du LDAP
- Suppression des systèmes d'alerte. Le controle des données se fait si nécessaire lors de la maj des états
- A revoir : Système d'alerte pour des données non critique (voir si nécessaires car en places dans les versions précédentes mais jamais réelement utilisé/lu).

1.0.19 (13/11/2024)
------------------

[Mise à jours]
- Passage complet a BDD-Admin
- Remplacement des Statut par les états
- Suppression des alertes (A revoir la gestions compléte)
- Début de suppressions de tables obselletes (annuaire ...)
- Mise au propres des mails automatiques

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

- Modification des procédures de recalcul des ordres d'affectations
- Début de passage a UnicaenEtat

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
