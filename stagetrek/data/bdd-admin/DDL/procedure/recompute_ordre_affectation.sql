CREATE OR REPLACE PROCEDURE public.recompute_ordre_affectation(sessionid integer)
 LANGUAGE plpgsql
AS $procedure$
BEGIN
	WITH stage_update AS (
		SELECT stage.id                     AS stage_id
			 , stage.session_stage_id       AS session_id
			 , CASE WHEN stage.score_pre_affectation IS NOT NULL THEN stage.score_pre_affectation
			                                                     ELSE 0
			   END                          AS score_pre_affectation
			 , stage.ordre_affectation_auto AS rang_auto
			 , stage.ordre_affectation_ow   AS rang_manuel
			 , setseed( cast( '0.' || stage.session_stage_id AS float ) ) -- pour fixé l'aléa éventuel
			 , random( )                    AS alea -- on attribue un aléa au stage pour gérer des cas d'égalité
		FROM stage
		WHERE session_stage_id = sessionid
		  and is_stage_secondaire = FALSE
	)
-- pour chaque session, on détermine une places par rang.
	   , classement_possible AS (
		SELECT row_number( ) OVER (PARTITION BY session_id) AS rang
			 , session_id                                   AS session_id
		FROM stage_update
	)
-- Etape 2 Recherche des stages ayant un rang manuel, avec surcharge éventuel pour les cas d'égalité
	   , pre_classement_manuel AS (
		SELECT row_number( ) OVER (PARTITION BY s.session_id) AS numero
			 , s.session_id
			 , s.stage_id
			 , s.rang_manuel
			 -- delta permettant de gerer des cas d'égalité basé sur le rang auto (lui même comprenant un aléa)
			 , RANK( )
			   OVER (PARTITION BY s.session_id
				   , s.rang_manuel ORDER BY s.rang_manuel
				   , s.rang_auto, s.alea
				   , stage_id) - 1
			                                                  AS delta
		FROM stage_update AS s
		WHERE rang_manuel IS NOT NULL
		GROUP BY s.session_id, s.stage_id, s.rang_manuel, s.rang_auto, s.alea
		ORDER BY session_id
		       , rang_manuel
		       , delta
	)
--    Classement_manuel donne le classement des stages ayant un champ ordre_ow + un décalage éventuel en cas d'égalité
--    ie : 2 stage ayant un ordre_ow de 2, l'un se retrouvera avec un ordre recalculé de 3
	   , classement_manuel_tmp AS (
		WITH RECURSIVE recursion AS (
			SELECT t1.*
				 , CASE
				WHEN t1.rang_manuel IS NOT NULL
					THEN cast( t1.rang_manuel AS int )
					ELSE 0
				   END AS rang_recalcule
			FROM pre_classement_manuel t1
			WHERE numero = 1
			UNION
			SELECT t2.*
				 , cast( greatest( cast( t2.rang_manuel AS int ) +
					                   cast( t2.delta AS int ),
				                   recursion.rang_recalcule +
					                   1 ) AS int ) AS rang_recalcule
			FROM pre_classement_manuel t2
				     JOIN recursion ON recursion.session_id =
				t2.session_id AND
				recursion.numero + 1 =
					t2.numero
			WHERE t2.numero != 1
		)
		SELECT *
		FROM recursion
	)
-- application du rang manuel au classements
	   , classement_manuel AS (
		SELECT t2.rang, t1.session_id, t1.stage_id
		FROM classement_manuel_tmp t1
			     JOIN classement_possible t2
			     ON t1.session_id = t2.session_id AND t1.rang_recalcule = t2.rang
--     Gestions des cas particulier : des stages ayant un rang manuel supérieur au nombre de stage
		UNION
		SELECT t1.rang_recalcule, t1.session_id, t1.stage_id
		FROM classement_manuel_tmp t1
			     LEFT JOIN classement_possible t2
			     ON t1.session_id = t2.session_id AND t1.rang_recalcule = t2.rang
		WHERE t2.session_id IS NULL
	)
-- 	Etapes 2 : on classe les stage automatique
-- On ordonne les stages n'ayant pas de classement manuel en fonction de leurs classement automatique pré-calculé.
	   , classement_auto_tmp AS (
		SELECT row_number( ) OVER (PARTITION BY s.session_id ORDER BY rang_auto ) AS numero
			 , s.stage_id
			 , s.session_id
			 , s.score_pre_affectation
			 , CASE
			WHEN s.rang_auto IS NOT NULL THEN s.rang_auto
			                             ELSE 1000 -- pour passer aprés ceux ayant un rang automatique déjà calculé
-- 	                                 Plus le score_pre_ffectation, sinon un aléa et sinon l'id du stage
					                             + RANK( )
					                               OVER (PARTITION BY s.session_id ORDER BY s.score_pre_affectation, s.alea, s.stage_id)
			   END                                                                AS rang_auto
			 , s.rang_manuel
		FROM stage_update s
		WHERE s.rang_manuel IS NULL
	)
--    On associe le classement automatique en fonction des classements qui ne sont pas pris par des position manuel
-- On liste les classements qui ne sont pas encore pris par des cas manuel et on les affecte aux classement automatique
	   , classement_auto_disponible AS (
		SELECT row_number( ) OVER (PARTITION BY c1.session_id) AS numero
			 , c1.*
		FROM classement_possible c1
			     LEFT JOIN classement_manuel c2 ON c1.session_id = c2.session_id AND c1.rang = c2.rang
		WHERE c2.stage_id IS NULL
		ORDER BY c1.rang
	)
	   , classement_auto AS (
		SELECT c2.rang, c1.session_id, c1.stage_id, c1.rang_auto, c1.score_pre_affectation
		FROM classement_auto_tmp c1
			     JOIN classement_auto_disponible c2
			     ON c1.session_id = c2.session_id AND c1.numero = c2.numero
	)
-- union des informations
	   , classement_final AS (
		SELECT s.session_id
			 , s.stage_id AS stage_id
			 , s.score_pre_affectation
			 , s.rang_auto
			 , s.rang_manuel
			 , CASE
			WHEN classement_manuel.rang IS NOT NULL THEN classement_manuel.rang
			WHEN classement_auto.rang IS NOT NULL   THEN classement_auto.rang
			   END        AS classement
		FROM stage_update s
			     LEFT JOIN classement_manuel ON classement_manuel.stage_id = s.stage_id
			     LEFT JOIN classement_auto ON classement_auto.stage_id = s.stage_id
	)
-- maj des classement
	UPDATE stage s
	SET ordre_affectation = c.classement
	FROM classement_final c
	WHERE s.id = c.stage_id;
-- Adaptation pour les stages secondaire

	UPDATE stage s
	SET ordre_affectation = s2.ordre_affectation
	  , ordre_affectation_ow = s2.ordre_affectation_ow
	  , ordre_affectation_auto = s2.ordre_affectation_auto
	FROM stage s2
	WHERE s2.id = s.stage_principal_id
	  AND s.is_stage_secondaire = TRUE;
END;
$procedure$