-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- VIEW ---------------------
-- ------------------------------
-- Vue permettant de d'aglomérer des données sur les contraintes portant indirectement sur des terrains via leurs groupes
CREATE OR REPLACE VIEW v_contrainte_cursus_niveau_etude_complete AS
	WITH contraites_cursus_active AS (
		SELECT *
		FROM contrainte_cursus
		WHERE contrainte_cursus.date_debut <= now( ) AND now( ) <= contrainte_cursus.date_fin
	)
	   , restriction_terrain_niveau_etude AS (
		SELECT contraites_cursus_active.id                           AS contrainte_cursus_id
			 , contraites_cursus_active.terrain_stage_id          AS terrain_stage_id
			 , contrainte_cursus_niveau_etude_linker.niveau_etude_id AS niveau_etude_id
		FROM contraites_cursus_active
			     JOIN contrainte_cursus_niveau_etude_linker
			     ON contraites_cursus_active.id = contrainte_cursus_niveau_etude_linker.contrainte_cursus_id
		WHERE terrain_stage_id IS NOT NULL
		UNION
		SELECT contraites_cursus_active.id                           AS contrainte_cursus_id
			 , terrain_stage.id                                   AS terrain_stage_id
			 , contrainte_cursus_niveau_etude_linker.niveau_etude_id AS niveau_etude_id
		FROM terrain_stage
			     JOIN categorie_stage ON terrain_stage.categorie_stage_id = categorie_stage.id
			     JOIN contraites_cursus_active ON categorie_stage.id = contraites_cursus_active.categorie_stage_id
			     JOIN contrainte_cursus_niveau_etude_linker
			     ON contraites_cursus_active.id = contrainte_cursus_niveau_etude_linker.contrainte_cursus_id
	)
	   , categorie_nb_terrain AS (
		SELECT terrain_stage.categorie_stage_id, count( terrain_stage.id ) AS nb_terrain_categorie
		FROM terrain_stage
		GROUP BY terrain_stage.categorie_stage_id
	)
	   , categorie_contrainte_terrain AS (
		SELECT contrainte_cursus_niveau_etude_linker.contrainte_cursus_id AS contrainte_cursus_id
			 , terrain_stage.categorie_stage_id                        AS categorie_stage_id
			 , contrainte_cursus_niveau_etude_linker.niveau_etude_id      AS niveau_etude_id
			 , count( terrain_stage.id )                               AS nb_terrain_contraint
		FROM contraites_cursus_active
			     JOIN contrainte_cursus_niveau_etude_linker
			     ON contraites_cursus_active.id = contrainte_cursus_niveau_etude_linker.contrainte_cursus_id
			     JOIN terrain_stage ON terrain_stage.id = contraites_cursus_active.terrain_stage_id
		GROUP BY contrainte_cursus_niveau_etude_linker.contrainte_cursus_id, terrain_stage.categorie_stage_id
		       , contrainte_cursus_niveau_etude_linker.niveau_etude_id
	)
	   , restriction_categorie_niveau_etude AS (
		SELECT contraites_cursus_active.id                           AS contrainte_cursus_id
			 , contraites_cursus_active.categorie_stage_id           AS categorie_stage_id
			 , contrainte_cursus_niveau_etude_linker.niveau_etude_id AS niveau_etude_id
		FROM contraites_cursus_active
			     JOIN contrainte_cursus_niveau_etude_linker
			     ON contraites_cursus_active.id = contrainte_cursus_niveau_etude_linker.contrainte_cursus_id
		WHERE contraites_cursus_active.categorie_stage_id IS NOT NULL
		UNION
		-- Les catégories dont la totalité des terrains sont contraint
		SELECT categorie_contrainte_terrain.contrainte_cursus_id AS contrainte_cursus_id -- null car la catégorie est contrainte par un ensemble de contrainte
			 , categorie_contrainte_terrain.categorie_stage_id   AS categorie_stage_id
			 , categorie_contrainte_terrain.niveau_etude_id      AS niveau_etude_id
		FROM categorie_nb_terrain
			     JOIN categorie_contrainte_terrain
			     ON categorie_nb_terrain.categorie_stage_id = categorie_contrainte_terrain.categorie_stage_id
		WHERE categorie_nb_terrain.nb_terrain_categorie = categorie_contrainte_terrain.nb_terrain_contraint
	)
	SELECT restriction_categorie_niveau_etude.contrainte_cursus_id
		 , restriction_categorie_niveau_etude.niveau_etude_id
		 , restriction_categorie_niveau_etude.categorie_stage_id AS categorie_stage_id
		 , NULL::int                                             AS terrain_stage_id
	FROM restriction_categorie_niveau_etude
	UNION
	SELECT restriction_terrain_niveau_etude.contrainte_cursus_id
		 , restriction_terrain_niveau_etude.niveau_etude_id
		 , NULL::int                                            AS categorie_stage_id
		 , restriction_terrain_niveau_etude.terrain_stage_id AS terrain_stage_id
	FROM restriction_terrain_niveau_etude;


-- ----
-- Détection de contradictions entre les contraintes
-- ----
create or replace view V_CONTRAINTES_CONTRADICTOIRES as
	(
	with dates as
		(
			select row_number() over (order by date) as num, date from (
				                                                           select distinct date_debut as date
				                                                           from contrainte_cursus
				                                                           union
				                                                           select distinct date_fin as date
				                                                           from contrainte_cursus) as tmp
		),
		periodes as (
			select  row_number() over (order by d1.date) as id,
				d1.date as date_debut, d2.date as date_fin from dates d1
					                                                left join dates d2 on (d1.num+1) = d2.num

		)
-- Liens entre les contraintes et les périodes
	   , contraintes_periodes as (
			select c.id as contrainte_id, p.id as periode_id from contrainte_cursus c
			                                                    , periodes p
			where not(c.date_fin <= p.date_debut)
			  and not (c.date_debut >= p.date_fin)
		)
	   , contraintes as (
			select
				c.id as contrainte_id,
				cp.periode_id as periode_id
				 ,c.portee as portee
				 , case
				when t.categorie_stage_id is not null then t.categorie_stage_id
				                                      else c.categorie_stage_id end         as categorie_id,
				c.terrain_stage_id                     as terrain_id
				 , COALESCE(c.nombre_de_stage_min, 0)       as min
				 , COALESCE(c.nombre_de_stage_max, 999)       as max
			from contrainte_cursus c
				     left join terrain_stage t on t.id = c.terrain_stage_id
				     join contraintes_periodes cp on cp.contrainte_id = c.id
		)
--    Recherches des contraintes ayant les mêmes portées : 2 général ou même catégorie ou même entities
	   , tuples as (select
		                c1.periode_id as periode_id
	                     ,c1.contrainte_id                                                               as c1_id
	                     , c2.contrainte_id                                                               as c2_id
	                     , c1.portee                                                           as c1_portee
	                     , c2.portee                                                           as c2_portee
	                     , c1.categorie_id                                                     as c1_categorie_id
	                     , c2.categorie_id                                                     as c2_categorie_id
	                     , c1.terrain_id                                                       as c1_terrain_id
	                     , c2.terrain_id                                                       as c2_terrain_id
	                     , c1.min                                                              as c1_min
	                     , c2.min                                                              as c2_min
	                     , c1.max                                                              as c1_max
	                     , c2.max                                                              as c2_max
	                from contraintes c1,
		                contraintes c2
	                where (-- les périodes doivent être compatible
		                c1.periode_id = c2.periode_id
		                )
			          and (c1.contrainte_id < c2.contrainte_id) -- pour n'avoir qu'une seul fois un tuples
		              -- les contraintes doivent portée sur des catégories/terrains compatible
			          and ( c1.portee=c2.portee)
			          and ( c1.portee=1
			                or (c1.portee= 2 and c1.categorie_id = c2.categorie_id)
			                or (c1.portee= 3 and c1.terrain_id = c2.terrain_id)
		                )
		)
--    Contradiction direct = même portée avec des bornes contradictoires
	   , contradiction_direct as (
			select periode_id, c1_id, c2_id, c1_min, c1_max, c2_min, c2_max
				 ,(c1_min > c2_max or c2_min > c1_max) as contradiction
			from tuples
		)
	   , contraintes_terrains as (
			select p.id as periode_id
				 , t.categorie_stage_id as categorie_id
				 , t.id as terrain_id
				 , COALESCE(c.min, 0) as min
				 , COALESCE(c.max, 999) as max
			from periodes p
				     join terrain_stage t on true
				     left join contraintes c on c.periode_id = p.id and c.terrain_id = t.id
		)
	   , bornes_categories_from_terrains as (
			select periode_id
				 , categorie_id
				 , sum(min) as min
				 , sum(max) as max
			from contraintes_terrains
			group by categorie_id, periode_id
		),
		bornes_categories_from_categorie as (
			select p.id as periode_id
				 , cat.id as categorie_id
				 , max(COALESCE(c.min, 0)) as min
				 , min(COALESCE(c.max, 999)) as max
			from  periodes p
				      join categorie_stage cat on true
				      left join contraintes c on c.portee=2 and c.categorie_id = cat.id and c.periode_id = p.id
			group by p.id, cat.id
		)
--    Une catégories est en contradiction indirect si les bornes déterminée par les contraintes sur les terrains contredisent les bornes définies par les catégories
	   , categorie_contradiction_indirect as (
			select cc.periode_id, cc.categorie_id
				 , cc.min as min_by_cat, cc.max as max_by_cat
				 , ct.min as min_by_ter, ct.max as max_by_ter
				 ,(ct.max < cc.min or cc.max < ct.min
					or ct.max < ct.min or cc.max < cc.min -- théoriquement pas possible
				)
				          as contradiction
			from bornes_categories_from_categorie cc
				     join bornes_categories_from_terrains ct
				     on cc.periode_id = ct.periode_id
						     and cc.categorie_id = ct.categorie_id
		)
--    Même problèmes sur les contraintes globals
	   , bornes_global_by_categories as (
			select cc.periode_id
				 , sum(greatest(COALESCE(cc.min,0), COALESCE(ct.min, 0))) as min
				 , sum(greatest(COALESCE(cc.max,999), COALESCE(ct.max, 999))) as max
			from bornes_categories_from_categorie cc
				     join bornes_categories_from_terrains ct
				     on cc.periode_id = ct.periode_id
						     and cc.categorie_id = ct.categorie_id
			group by cc.periode_id
		)
	   , bornes_global_by_contraintes_g as (
			select p.id as periode_id
				 , max(COALESCE(c.min, 0)) as min
				 , min(COALESCE(c.max, 999)) as max
			from periodes p
				     left join contraintes c on p.id = c.periode_id and c.portee=1
			group by p.id
		)
	   , contradiction_global_indirect as (
			select cc.periode_id
				 , cg.min as min_by_g, cg.max as max_by_g
				 , cc.min as min_by_cat, cc.max as max_by_cat
				 ,(cg.max < cc.min or cc.max < cg.min
					or cg.max < cg.min or cc.max < cg.min -- théoriquement pas possible
				)
				          as contradiction
			from bornes_global_by_contraintes_g cg
				     join bornes_global_by_categories cc
				     on cc.periode_id = cg.periode_id
		)
	   , contradiction_indirect as (
			select c.*
				 , (cg.contradiction or coalesce(cc.contradiction, false)) as contradiction
				 , cg.contradiction as contradiction_general
				 , cg.min_by_g, cg.max_by_g
				 , coalesce(cc.contradiction, false) as contradiction_indirect_categorie
				 , coalesce(cc.min_by_cat, 0) as min_by_cat, coalesce(cc.max_by_cat, 999) as max_by_cat
				 , coalesce(cc.min_by_ter, 0) as min_by_ter, coalesce(cc.max_by_ter, 999) as max_by_ter
			from contraintes c
				     join contradiction_global_indirect cg on cg.periode_id = c.periode_id
				     left join categorie_contradiction_indirect cc
				     on cc.periode_id = c.periode_id and c.categorie_id = cc.categorie_id
		)
-- Regroupement des contradiction direct et indirect sur chaque contraintes
	   , contradiction as (
			select c.contrainte_id
				 , c.portee, c.categorie_id, c.terrain_id, c.min, c.max
				 , bool_or(coalesce(cd.contradiction, false)) or bool_or(coalesce(cdi.contradiction, false))
				                                               as contradiction
				 , bool_or(coalesce(cd.contradiction, false)) as contradiction_direct
				 , bool_or(coalesce(cdi.contradiction, false)) as contradiction_indirect
			from contraintes c
				     left join contradiction_direct cd on (c.contrainte_id = cd.c1_id
					or c.contrainte_id = cd.c2_id
				)
				     left join contradiction_indirect cdi on c.contrainte_id = cdi.contrainte_id
			group by c.contrainte_id, c.portee, c.categorie_id, c.terrain_id, c.min, c.max
		)
	select c.contrainte_id from contradiction c
	where c.contradiction = true
);
