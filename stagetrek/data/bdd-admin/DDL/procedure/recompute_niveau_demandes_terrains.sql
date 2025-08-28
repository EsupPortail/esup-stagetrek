CREATE OR REPLACE PROCEDURE public.recompute_niveau_demandes_terrains(IN sessionid integer)
 LANGUAGE plpgsql
AS $procedure$
BEGIN
	with terrains_places as (
		SELECT session_stage_id
			 , terrain_stage_id
			 , nb_places_ouvertes
		FROM session_stage_terrain_linker tl
			     join terrain_stage t on tl.terrain_stage_id = t.id
		where t.terrain_principal = true
		  and session_stage_id = sessionid
	)
-- Application du même principe que pour les terrains pour le reste
	   , demandes as (
		SELECT session_stage_id, terrain_stage_id, p.rang, count(*) as nb_demande
			 ,case when c.rang is null then 0.1
			       when c.cout <0.1 then 0.1
			                           else c.cout
			  end as cout_rang
		from preference p
			     join stage s on p.stage_id = s.id
			     join session_stage ss on ss.id = s.session_stage_id
			     LEFT JOIN parametre_cout_affectation c on c.rang = p.rang
		GROUP BY session_stage_id, terrain_stage_id, p.rang, c.rang, c.cout
	)
--    Calcul des score's
	   , scores as (
		SELECT tl.session_stage_id, tl.terrain_stage_id,
			tl.nb_places_ouvertes,
			case when d.terrain_stage_id is null then 0
			                                     else sum(d.nb_demande) end
				as nb_demande,
			case -- Cas d'un terrains fermé, score de -1
				when tl.nb_places_ouvertes =0 then -1
-- 	       Pas de demande
				when d.terrain_stage_id is null then 0
				else  sum(d.nb_demande*d.cout_rang)/tl.nb_places_ouvertes
			end as score
		from terrains_places tl
			     left join demandes d on d.session_stage_id = tl.session_stage_id
				and d.terrain_stage_id = tl.terrain_stage_id
		GROUP BY tl.session_stage_id,tl.terrain_stage_id, d.terrain_stage_id,  tl.nb_places_ouvertes
	)
--    On classe les terrains par scores
	   , classements as (
		SELECT s.session_stage_id, s.terrain_stage_id
			 ,s.nb_demande
			 , s.score
			 , RANK( )
			   OVER (PARTITION BY s.session_stage_id ORDER BY s.score desc)
			                                                                                                                                    as rang_sans_alea
			 -- on fixe la seed de l'aléatoire pour ne pas avoir de différences
			 , setseed( cast( '0.' || s.session_stage_id AS float ) )
			 , RANK( )  OVER (PARTITION BY s.session_stage_id ORDER BY s.score desc, (s.score, s.score+ random( )) desc, terrain_stage_id desc) as rang
		from scores s
		where s.score > 0 -- on ignore ainsi les terrains fermées et sans demande
	),
		decile as (
		SELECT session_stage_id, terrain_stage_id, nb_demande, rang, score
			 ,(COUNT(*) OVER (PARTITION BY session_stage_id))/10.0 AS cinquieme
			 , ceil(rang/((COUNT(*) OVER (PARTITION BY session_stage_id))/10.0)) as decile
		FROM classements
	)
-- Agregation
	   , data as (
		SELECt t.session_stage_id, t.terrain_stage_id, t.nb_places_ouvertes
-- 	, case when d.nb_demande is null then 0 else d.nb_demande end as nb_demande
			 ,s.score, s.nb_demande
			 , case when d.rang is null then -1 else d.rang end as rang
			 , case when d.decile is null then -1 else d.decile end as decile
			 , case when t.nb_places_ouvertes = 0 then 'ferme'
			        when d.rang is null then 'no_demande'
			        when d.decile = 1 then 'rang_1'
			        when d.decile = 2 then 'rang_2'
			        when d.decile = 3 then 'rang_3'
			        when d.decile = 4 then 'rang_4'
			        when d.decile = 5 then 'rang_5'
			        when d.decile = 6 then 'rang_6'
			        when d.decile = 7 then 'rang_7'
			        when d.decile = 8 then 'rang_8'
			        when d.decile = 9 then 'rang_9'
			        when d.decile = 10 then 'rang_10'
			                                      else 'n/a'
			   end as code_niveau_demande

		from terrains_places t
			     left join scores s  on t.terrain_stage_id = s.terrain_stage_id and s.session_stage_id = t.session_stage_id
			     left join decile d on s.terrain_stage_id = d.terrain_stage_id and d.session_stage_id = t.session_stage_id
	)
	update session_stage_terrain_linker tl
	set terrain_stage_niveau_demande_id = data.niveau_demande_id
	from (
		     select
			     data.session_stage_id, data.terrain_stage_id
			      ,n.id as niveau_demande_id
		     from data
			          left join terrain_stage_niveau_demande n on n.code = data.code_niveau_demande

	     ) data
	where data.terrain_stage_id = tl.terrain_stage_id and data.session_stage_id  = tl.session_stage_id;


-- Cas des terrains secondaires
	with terrains_places as (
		SELECT session_stage_id
			 , terrain_stage_id
			 , nb_places_ouvertes
		FROM session_stage_terrain_linker tl
			     join terrain_stage t on tl.terrain_stage_id = t.id
		where t.terrain_principal = false
		  and session_stage_id = sessionid
	)
-- On filtre les préférences pour ne prendre que la premiére de chaque étudiant pour un meme terrain, une même session
	   , preference as (
		SELECT stage_id, terrain_stage_secondaire_id as terrain_stage_id,
			min(rang) as rang
		FROM preference
		WHERE terrain_stage_secondaire_id IS NOT NULL
		GROUP BY stage_id, terrain_stage_secondaire_id
	)
-- Application du même principe que pour les terrains pour le reste
	   , demandes as (
		SELECT session_stage_id, terrain_stage_id, p.rang, count(*) as nb_demande
			 ,case when c.rang is null then 0.1
			       when c.cout <0.1 then 0.1
			                           else c.cout
			  end as cout_rang
		from preference p
			     join stage s on p.stage_id = s.id
			     join session_stage ss on ss.id = s.session_stage_id
			     LEFT JOIN parametre_cout_affectation c on c.rang = p.rang
		GROUP BY session_stage_id, terrain_stage_id, p.rang, c.rang, c.cout
	)
--    Calcul des score's
	   , scores as (
		SELECT tl.session_stage_id, tl.terrain_stage_id,
			tl.nb_places_ouvertes,
			case when d.terrain_stage_id is null then 0
			                                     else sum(d.nb_demande) end
				as nb_demande,
			case -- Cas d'un terrains fermé, score de -1
				when tl.nb_places_ouvertes =0 then -1
-- 	       Pas de demande
				when d.terrain_stage_id is null then 0
				else  sum(d.nb_demande*d.cout_rang)/tl.nb_places_ouvertes
			end as score
		from terrains_places tl
			     left join demandes d on d.session_stage_id = tl.session_stage_id
				and d.terrain_stage_id = tl.terrain_stage_id
		GROUP BY tl.session_stage_id,tl.terrain_stage_id, d.terrain_stage_id,  tl.nb_places_ouvertes
	)
--    On classe les terrains par scores
	   , classements as (
		SELECT s.session_stage_id, s.terrain_stage_id
			 ,s.nb_demande
			 , s.score
			 , RANK( )
			   OVER (PARTITION BY s.session_stage_id ORDER BY s.score desc)
			                                                                                                                                    as rang_sans_alea
			 -- on fixe la seed de l'aléatoire pour ne pas avoir de différences
			 , setseed( cast( '0.' || s.session_stage_id AS float ) )
			 , RANK( )  OVER (PARTITION BY s.session_stage_id ORDER BY s.score desc, (s.score, s.score+ random( )) desc, terrain_stage_id desc) as rang
		from scores s
		where s.score > 0 -- on ignore ainsi les terrains fermées et sans demande
	),
		decile as (
		SELECT session_stage_id, terrain_stage_id, nb_demande, rang, score
			 ,(COUNT(*) OVER (PARTITION BY session_stage_id))/10.0 AS cinquieme
			 , ceil(rang/((COUNT(*) OVER (PARTITION BY session_stage_id))/10.0)) as decile
		FROM classements
	)
-- Agregation
	   , data as (
		SELECt t.session_stage_id, t.terrain_stage_id, t.nb_places_ouvertes
-- 	, case when d.nb_demande is null then 0 else d.nb_demande end as nb_demande
			 ,s.score, s.nb_demande
			 , case when d.rang is null then -1 else d.rang end as rang
			 , case when d.decile is null then -1 else d.decile end as decile
			 , case when t.nb_places_ouvertes = 0 then 'ferme'
			        when d.rang is null then 'no_demande'
			        when d.decile = 1 then 'rang_1'
			        when d.decile = 2 then 'rang_2'
			        when d.decile = 3 then 'rang_3'
			        when d.decile = 4 then 'rang_4'
			        when d.decile = 5 then 'rang_5'
			        when d.decile = 6 then 'rang_6'
			        when d.decile = 7 then 'rang_7'
			        when d.decile = 8 then 'rang_8'
			        when d.decile = 9 then 'rang_9'
			        when d.decile = 10 then 'rang_10'
			                                      else 'n/a'
			   end as code_niveau_demande
		from terrains_places t
			     left join scores s  on t.terrain_stage_id = s.terrain_stage_id and s.session_stage_id = t.session_stage_id
			     left join decile d on s.terrain_stage_id = d.terrain_stage_id and d.session_stage_id = t.session_stage_id
	)

	update session_stage_terrain_linker tl
	set terrain_stage_niveau_demande_id = data.niveau_demande_id
	from (
		     select
			     data.session_stage_id, data.terrain_stage_id
			      ,n.id as niveau_demande_id
		     from data
			          left join terrain_stage_niveau_demande n on n.code = data.code_niveau_demande

	     ) data
	where data.terrain_stage_id = tl.terrain_stage_id and data.session_stage_id  = tl.session_stage_id;


END;
$procedure$