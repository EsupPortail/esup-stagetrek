CREATE OR REPLACE PROCEDURE public.recompute_ordre_affectation_auto(sessionid integer)
 LANGUAGE plpgsql
AS $procedure$
BEGIN
	with stage as (
		SELECT * from stage where session_stage_id = sessionid
		                      and is_stage_secondaire = false
	)
		--    classe les stages en fonction du score de l'étudiant + un aléa en cas d'égalité sur le score
	   , classement_by_score as (
		SELECT id AS stage_id
			 , score_pre_affectation
			 , RANK( )
			   OVER (PARTITION BY stage.session_stage_id ORDER BY stage.score_pre_affectation)
			      AS rang_score
			 -- on fixe la seed de l'aléatoire pour ne pas avoir de différences
			 , setseed( cast( '0.' || stage.session_stage_id AS float ) )
			 ,
			 -- Marche plutot bien, en l'abscence de rajout d'un étudiant
			 -- Forte modulation possible tout de fois a l'ajout d'un étudiant pour la session de stage 1 car tout les étudiants on un score initial de 0
			 -- Classement des score avec en cas d'égalité une distinction aléatoire (seed =id de la session) et dans le cas improbable d'une nouvelle égalité, priorité a l'étudiant ayant le plus petit stage_id
					RANK( )
					OVER (PARTITION BY stage.session_stage_id ORDER BY stage.score_pre_affectation, random( ), stage.id)
                  AS rang_score_alea
		FROM stage
	)
	UPDATE stage s
	set ordre_affectation_auto = c.rang_score_alea
	from classement_by_score c
	where s.id = c.stage_id;
END;
$procedure$