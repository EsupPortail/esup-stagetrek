-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------


-- TODo : a faire en PHP
-- 2 procédures pour le calcul des scores :
-- 1 qui calcul le score automatique
-- Appelé automatique 1 seul fois : lorsque le calul n'as pas encore été fait et que l'on est a la bonne date
-- Possibilité de relancer manuellement le calcul suite a un recalcul des scores, mais fortement déconseillé
-- la seconde détermine l'ordre effectif, a lancer après le calcul des ordres automatique et lors du changment manuel d'un rang
-- Eviter de le faire après la fin d'une phase d'affectation car celà n'aurais pas de sens
CREATE OR REPLACE PROCEDURE recompute_ordre_affectation_auto(sessionid integer)
	LANGUAGE plpgsql
AS
$$
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
$$;

CREATE OR REPLACE PROCEDURE recompute_ordre_affectation(sessionid integer)
	LANGUAGE plpgsql
AS
$$
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
$$;



-- ------------------------------------------
-- -- Maj de
-- création automatiques des entités manquantes :
--  -- stages pas encore construit
--  -- affectation,
--  -- validation
--  -- stage secondaire
-- Numéro des stages
-- ------------------------------------------


-- --------------------------------------------------
-- -- Calcul automatique des ordres d'affectations
-- -- distinct de la maj classique pour des raisons de performences
-- --------------------------------------------------

--------------------------------------------------------
-- Vue séparer calculant le score des étudiants
-- calcul assez lourd
-- facilite l'extraction des résultat
---------------------------------------------------------
drop view if exists  v_update_stage_score;
create or replace view v_update_stage_score as
	(
	with stage as (select stage.id
	                    , stage.etudiant_id
	                    , stage.session_stage_id
	                    , cast(stage.numero_stage as int) as numero_stage -- pour distinguer les stages principaux des stages secondaires
	                    , ss.date_commission + interval '1 DAY' as date_fin_commission
	                    , stage.score_pre_affectation -- requis après pour comparaison des nouvelles données
	                    , stage.score_post_affectation -- requis après pour comparaison des nouvelles données
	               from stage
		                    join session_stage ss on ss.id = stage.session_stage_id
--                On ignore les stages secondaires
                   where stage.is_stage_secondaire=false
	)
-- Couts des affectations
	   , cout_affectation as (select stage.id                                                                           as stage_id,
		                          stage.numero_stage                                                                 as numero_stage,
		                          a.id                                                                               as affectation_id,
		                          a.cout_terrain,
		                          a.bonus_malus,
			                       -- si l'affectation n'est pas validé et la date de fin de la procédure d'affectation n'est pas dépassé, cout null
		                          case
			                          when (a.validee and stage.date_fin_commission <= now())
				                          then a.cout end                                                            as cout_affectation
	                          from stage
		                               left join affectation_stage a on stage.id = a.stage_id)
	   , calcul as (with recursive recursion as (
		-- Cas du 1er stages
		select stage.id              as stage_id
			 , stage.etudiant_id     as etudiant_id
			 , stage.numero_stage
			 ------------
			 -- Score pré-affectation
			 -- 0 nécéssairement pour le stage 1
			 ------------
			 , cast(0 as float)      as score_pre_affectation
			 ------------
			 -- Cout de l'affectation
			 ------------
			 , cout.cout_affectation as cout_affectation
			 ------------
			 -- Score Post-Affectation
			 ------------
			 , case
			-- Pas encore de score
			when stage.date_fin_commission > now() then null
			-- 1er stage non affecté, cout post = cout pré = 0
			when cout.cout_affectation is null then 0
			                                       else cout.cout_affectation
			   end                      as score_post_affectation
			 ------------
			 -- autres valeurs nécessaires pour le calcul des scores basé sur la moyenne
			 ------------
			 , case
			-- cas d'un 1er stage non affecté
			when cout.cout_affectation is null then 0
			-- Fonctionnement nominal
			                                   else 1
			   end                      as n
			 , case
			-- cas d'un 1er stage non affecté
			when cout.cout_affectation is null then 0
			-- Fonctionnement nominal
			                                   else cout.cout_affectation
			   end                      as somme_cout_affectation
		from stage
			     join cout_affectation cout on cout.stage_id = stage.id
		where stage.numero_stage = 1
		-- Cas des autres stages
		union
		--
		select stage.id                    as stage_id
			 , stage.etudiant_id           as etudiant_id
			 , stage.numero_stage
			 ------------
			 -- Score pré-affectation
			 ------------
			 , prec.score_post_affectation as score_pre_affectation
			 ------------
			 -- Cout de l'affectation
			 ------------
			 , cout.cout_affectation       as cout_affectation
			 ------------
			 -- Score Post-Affectation
			 ------------
			 , case
			-- Pas encore de score
			when stage.date_fin_commission > now() then null
			-- 1er stage non affecté, cout post = cout pré = 0
			when cout.cout_affectation is null then prec.score_post_affectation
			-- Fonctionnement nominal
			when (prec.n < 5) then (prec.somme_cout_affectation + cout.cout_affectation) / (prec.n + 1)
			                                       else (4 * prec.score_post_affectation + cout.cout_affectation) / 5
			   end                            as score_post_affectation
			 ------------
			 -- autres valeurs nécessaires pour le calcul des scores basé sur la moyenne
			 ------------
			 , case -- si le stage n'(4est pas affecté on n'incrémente pas n
			when cout.cout_affectation is null then prec.n
			                                   else prec.n + 1
			   end                            as n
			 , case
			-- si le stage n'est pas affecté on n'incrémente pas le total
			when cout.cout_affectation is null then prec.somme_cout_affectation
			                                   else prec.somme_cout_affectation + cout.cout_affectation
			   end                            as somme_cout_affectation
		from stage
			     join cout_affectation cout on cout.stage_id = stage.id
			     INNER JOIN recursion prec
			     on prec.etudiant_id = stage.etudiant_id and prec.numero_stage + 1 = stage.numero_stage)
	                select *
	                from recursion)
	   , precision as (select cast(parametre.value as integer) as value from parametre where parametre.code = 'precision_cout_affectation')
	   , scores as (select stage.id                                                               as stage_id,
		                stage.session_stage_id                                                 as session_stage_id,
		                stage.etudiant_id,
		                a.id                                                                   as affectation_id,
		                stage.numero_stage,
		                round(cast(a.cout_terrain as numeric), precision.value)                as cout_terrain,
		                round(cast(a.bonus_malus as numeric), precision.value)                 as bonus_malus,
		                round(cast(calcul.cout_affectation as numeric), precision.value)       as cout_affectation,
		                round(cast(calcul.score_pre_affectation as numeric), precision.value)  as score_pre_affectation,
		                round(cast(calcul.score_post_affectation as numeric), precision.value) as score_post_affectation
	                from stage
		                     join calcul on calcul.stage_id = stage.id
		                     join affectation_stage a on a.stage_id = stage.id,
		                precision
	)
	select stage_id, score_pre_affectation, score_post_affectation from scores
	except select id, score_pre_affectation, score_post_affectation from stage
	);


-- -----------------
-- Procédure -------
--------------------
CREATE or replace PROCEDURE update_stages()
	LANGUAGE plpgsql
AS
$$
BEGIN
	-- -- création automatiques des stages manquant
	insert into stage (session_stage_id, etudiant_id, stage_non_effectue)
		(select ssel.session_stage_id, ssel.etudiant_id, (ss.date_debut_stage < now())
		 from session_stage_etudiant_linker ssel
			      join session_stage ss on ss.id = ssel.session_stage_id
			      left join stage s on s.session_stage_id = ssel.session_stage_id and s.etudiant_id = ssel.etudiant_id
		 where s.id is null);

-- création automatique des affectations manquantes
	insert into affectation_stage (stage_id)
		(select s.id
		 from stage s left join affectation_stage a on s.id = a.stage_id
		 where a.id is null
--    LE stage ne doit pas être secondaire
		   and s.is_stage_secondaire = false
		);

-- Création automatiques des stages secondaires manquants :
	with stage_principaux as (
		select s.*
		from affectation_stage a
			     join stage s on a.stage_id = s.id
		where a.terrain_stage_secondaire_id is not null
		  and a.pre_validee = true
		  and a.validee = true
		  and a.stage_secondaire_id is null
	)
	insert into stage (session_stage_id, etudiant_id,
	                   numero_stage, is_stage_secondaire, stage_principal_id
						,ordre_affectation,  ordre_affectation_ow, ordre_affectation_auto
	)
		(
			select s.session_stage_id, s.etudiant_id
			    , (s.numero_stage+0.1) as numero_stage
				, true as is_stage_secondaire, s.id as stage_principal_id
                 , s.ordre_affectation, s.ordre_affectation_ow, s.ordre_affectation_auto
			from stage_principaux s
				     left join stage s2 on s2.etudiant_id = s.etudiant_id and s2.session_stage_id=s.session_stage_id
					and s2.is_stage_secondaire = true
			where s2.id is null -- on s'assure qu'il n'y a pas 2 stages secondaire
		);

-- Liens entre le stage secondaire et l'affectation
	with stage_secondaire as (select s.*
	                          from stage s
		                               left join stage sp on sp.id = s.stage_principal_id
	                          where s.is_stage_secondaire = true
		                        and sp.stage_secondaire_id is null
--                           and stage_principal_id is null
		--
	)
	   , affectation_to_update as (
	                          select a.id as affectation_id,
		                          s.id as stage_secondaire_id
	                          from affectation_stage a
		                               join stage_secondaire s on a.stage_id = s.stage_principal_id
	                          where a.stage_secondaire_id is null
	)
	update affectation_stage a
	set stage_secondaire_id = u.stage_secondaire_id
	from affectation_to_update u
	where u.affectation_id = a.id;

-- Liens entre le stage principal et le stage secondaire
	with stage_secondaire as (select s.*
	                          from stage s
		                               left join stage sp on sp.id = s.stage_principal_id
	                          where s.is_stage_secondaire = true
		                        and sp.stage_secondaire_id is null
	)
	   , stage_to_udate as (
	                          select s.id as stage_principal_id,
		                          s2.id as stage_secondaire_id
	                          from stage s
		                               join stage_secondaire s2 on s.id = s2.stage_principal_id
	                          where s.stage_secondaire_id is null
	)
	update stage s
	set stage_secondaire_id = u.stage_secondaire_id
	from stage_to_udate u
	where u.stage_principal_id = s.id;

-- création automatique des validations de stages manquantes
	insert into validation_stage (stage_id) (select s.id
	                                         from stage s
		                                              left join validation_stage vs on s.id = vs.stage_id
	                                         where vs.id is null);

-- suppression automatiques du lien entre les étudiants et les sessions qui ne sont plus d'actualité (l'étudiant n'est pas associé au groupe
	with to_delete as (select ssel.*
	                   from session_stage_etudiant_linker ssel
		                        join session_stage ss on ssel.session_stage_id = ss.id
		                        left join etudiant_groupe_linker egl
		                        on ss.groupe_id = egl.groupe_id and ssel.etudiant_id = egl.etudiant_id
	                   where egl.etudiant_id is null)
	delete from session_stage_etudiant_linker ssel
		using to_delete
	where to_delete.session_stage_id = ssel.session_stage_id and to_delete.etudiant_id=ssel.etudiant_id;

-- suppression automatiques des stages qui ne sont plus d'actualité
	with to_delete as
		     (select stage.id
		      from stage
			           left join session_stage_etudiant_linker ssel on
			      stage.session_stage_id = ssel.session_stage_id and
				      ssel.etudiant_id = stage.etudiant_id
		      where ssel.etudiant_id is null
		      union -- suppression des stages secondaires
		      select stage.id from stage
		          left join affectation_stage a on a.stage_secondaire_id = stage.id
		      where stage.is_stage_secondaire = true
			    and (a.id is null or a.terrain_stage_secondaire_id is null)
		     )
	delete
	from stage
		using to_delete
	where stage.id = to_delete.id;
-- Par cascade la suppression des stages devrais également supprimer les affectations et les validations

	update stage s
	set score_pre_affectation = u.score_pre_affectation,
		score_post_affectation = u.score_post_affectation
	from v_update_stage_score u
	where u.stage_id = s.id;

-- Cas des stages secondaire : on met a jours les scores et ordre d'affectation en se basant sur ceux du stages principales
	with data as (
		select s.id as stage_id
			 , p.score_pre_affectation
			 , p.score_post_affectation
			 , p.ordre_affectation
			 , p.ordre_affectation_auto
			 , p.ordre_affectation_ow
			 , p.stage_non_effectue
		from stage s
			     join stage p on s.stage_principal_id = p.id
		where s.is_stage_secondaire
	)
	update stage s
	set score_pre_affectation = d.score_pre_affectation,
		score_post_affectation = d.score_post_affectation,
		ordre_affectation = d.ordre_affectation,
		ordre_affectation_auto = d.ordre_affectation_auto,
		ordre_affectation_ow = d.ordre_affectation_ow,
		stage_non_effectue = d.stage_non_effectue
	from data d
	where d.stage_id = s.id;
END;
$$;