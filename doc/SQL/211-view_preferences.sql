-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- VIEW ---------------------
-- ------------------------------
-- Vue permettant de calculer les recommandations sur les préférences à partir des contraintes satisfaite, interdiction de terrains ... pour les étudiants
-- -- Note : a revoir proprement après la maj des terrains de stages associés

-- ---------
-- maj des recommandations pour la définition des préférences
-- --------
drop view if exists v_recommandation_contrainte_categorie;
drop view if exists v_recommandation_contrainte_terrain;