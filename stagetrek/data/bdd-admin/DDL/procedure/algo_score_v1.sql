CREATE OR REPLACE PROCEDURE public.algo_score_v1(sessionid integer)
 LANGUAGE plpgsql
AS $procedure$
declare
	affectationId int;
	terrainId
	              int;
	ordreAffectation
	              int;
BEGIN
	drop table if exists tmp_places_terrains;
	create
		temporary table tmp_places_terrains AS (
		select sstl.terrain_stage_id as terrain_id,
--             case when sstl.terrain_stage_id=66 then 0 else
--                 sstl.nb_places_ouvertes
--               end as nb_places_ouvertes
			nb_places_ouvertes,
			t.terrain_principal as terrain_principal
--             0 as nb_places_ouvertes
		from session_stage_terrain_linker sstl
			     join terrain_stage t on sstl.terrain_stage_id = t.id
		where sstl.session_stage_id=sessionId
		  -- on ingnore les terrains de stage inactif car il ne peuvent plus être affecté automatiquement
		  and t.actif
	);

	drop table if exists tmp_affectation;
	create
		temporary table tmp_affectation as (
		select a.id as affectation_id,
			s.ordre_affectation,
			0 as terrain_stage_id,
			0 as terrain_stage_secondaire_id,
			0.0 as cout,
			cast(0 as numeric) as cout_terrain,
			0.0 as bonus_malus,
			0 as rang_preference,
			a.validee,
			a.pre_validee
		from affectation_stage a
			     join stage s on a.stage_id = s.id
		where s.session_stage_id=sessionId
	);

-- Etape 1 : on détermine le terrain a affecté (et le terrain associé)
	for  affectationId, ordreAffectation in (
		-- le row_number sert a garentir l'ordre même s'il n'y a pas réelement de rang 1 de définit par exemple
		select a.id,  ROW_NUMBER() over(order by s.ordre_affectation) as ordre
		from affectation_stage a
			     join stage s on a.stage_id = s.id
		where s.session_stage_id=sessionId
		order by s.ordre_affectation
	)
		loop
			with affectation as (
				-- le row_number sert a garentir l'ordre même s'il n'y a pas réelement de rang 1 de définit par exemple
				select ordreAffectation as ordre, a.id as affectation_id, a.stage_id,
					a.terrain_stage_id, a.terrain_stage_secondaire_id, a.validee, a.pre_validee
				from affectation_stage a
					     join stage s on a.stage_id = s.id
				where a.id = affectationId
			)
			   , preference as (
				select a.affectation_id as affectation_id,
					case when pref.id is null then 1
					                          else row_number() over (partition by pref.stage_id order by pref.rang)
					end as rang,
					pref.terrain_stage_id, pref.terrain_stage_secondaire_id
				from affectation a
					     left join preference pref on a.stage_id = pref.stage_id
			)
			   , proposition as (select a.ordre,
				                     a.affectation_id,
				                     case
					                     when (a.validee or a.pre_validee) then a.terrain_stage_id
					                     when (place.nb_places_ouvertes is null or place.nb_places_ouvertes <= 0) then null
					                     else pref.terrain_stage_id
				                     end                  as terrain_id,
				                     case when (a.validee or a.pre_validee) then prefActuel.rang
				                          when (place.nb_places_ouvertes is null or place.nb_places_ouvertes <= 0) then null
					                     else pref.rang
				                     end as rang_preference,
					                  -- Terrain secondaire
				                     case
					                     when (a.validee or a.pre_validee) then a.terrain_stage_secondaire_id
					                     when (placeSecondaire.nb_places_ouvertes is null or placeSecondaire.nb_places_ouvertes <= 0) then null
					                     else pref.terrain_stage_secondaire_id
				                     end                  as terrain_stage_secondaire_id

			                     from affectation a
				                          left join preference pref on a.affectation_id = pref.affectation_id
				                          left join tmp_places_terrains place on place.terrain_id = pref.terrain_stage_id and place.terrain_principal=true
					                     and place.nb_places_ouvertes > 0
				                          left join tmp_places_terrains placeSecondaire on placeSecondaire.terrain_id = pref.terrain_stage_secondaire_id and placeSecondaire.terrain_principal=false
					                     and placeSecondaire.nb_places_ouvertes > 0
				                          left join preference prefActuel on prefActuel.affectation_id = a.affectation_id and prefActuel.terrain_stage_id = a.terrain_stage_id
			                     order by (place.nb_places_ouvertes > 0), pref.rang
			                     limit 1)
			   , update_place as (
				update tmp_places_terrains tmp
					set nb_places_ouvertes = tmp.nb_places_ouvertes - 1
					from proposition
					where proposition.terrain_id = tmp.terrain_id
			)
			   ,  update_place_secondaire as ( update tmp_places_terrains tmp
				set nb_places_ouvertes = tmp.nb_places_ouvertes - 1
				from proposition
				where proposition.terrain_stage_secondaire_id = tmp.terrain_id
			)
			   ,update_terrain as (
				update tmp_affectation tmp
					set terrain_stage_id = proposition.terrain_id,
						terrain_stage_secondaire_id = proposition.terrain_stage_secondaire_id,
						rang_preference = proposition.rang_preference
					from proposition
					where proposition.affectation_id = tmp.affectation_id
			)
			select proposition.ordre, proposition.terrain_id
			into ordreAffectation, terrainId
			from proposition;
		end loop;

-- Calcul et maj du cout des affectations
	with affectation as (select a.id                  as affectation_id,
		                     a.terrain_stage_id as terrain_id,
		                     a.terrain_stage_id,
		                     a.terrain_stage_secondaire_id,
		                     a.validee,
		                     a.pre_validee,
		                     a.cout_terrain,
		                     a.bonus_malus,
		                     a.cout
	                     from affectation_stage a
		                          join stage s on a.stage_id = s.id
	                     where s.session_stage_id = sessionId)
-- Calcul pour chaque terrain de stage, chaque rang du nombre d'étudiant ayant définit des préférences
	   , demande_terrain as (select t.id                                                    as terrain_id,
		                         case when pref.rang is null then 0 else pref.rang end   as rang,
		                         case when pref.rang is null then 0 else count(s.id) end as nb,
		                         case when cout.cout is null then 0 else cout.cout end   as cout
	                         from terrain_stage t
		                              join stage s on s.session_stage_id = sessionId
		                              left join preference pref on pref.terrain_stage_id = t.id and pref.stage_id = s.id
		                              left join parametre_cout_affectation cout on cout.rang = pref.rang
	                         group by t.id, pref.rang, cout.cout)
	   , cout_terrain_1 as (select t.terrain_id,
		                        case
			                        when nb_places_ouvertes > 0 then
				                        least(
						                        round(
								                        sum(nb * t.cout) / nb_places_ouvertes -
									                        pow(sum(nb * t.cout) / (nb_places_ouvertes * 10),
									                            cast(facteur_correcteur.value as numeric)),
								                        cast(precision.value as int)
						                        ), cast(borne_max.value as numeric)
				                        )
			                                                    else 0
		                        end as cout_terrain
	                        from demande_terrain t
		                             join session_stage_terrain_linker linker on
		                        linker.session_stage_id = sessionId
				                        and linker.terrain_stage_id = t.terrain_id
		                             join terrain_stage terrain on terrain.id = t.terrain_id
		                             join parametre facteur_correcteur
		                             on facteur_correcteur.code = 'facteur_correcteur_cout_terrain'
		                             join parametre
		                        precision
		                             on precision.code = 'precision_cout_affectation'
		                             join parametre borne_max on borne_max.code = 'cout_terrain_max'
		                             left join parametre_terrain_cout_affectation_fixe cout_fixe on t.terrain_id = cout_fixe.terrain_stage_id
	                        where cout_fixe.id is null
	                        -- terrain fermé ==> pas de cout pusique non affectable
-- cout fixe ajouter après
	                        group by t.terrain_id, terrain.preferences_autorisees,
		                        cast(facteur_correcteur.value as numeric), precision.value, borne_max.value, linker.nb_places_ouvertes, cout_fixe.id
	                                                         )
	   , cout_median as (
	                     select percentile_disc(0.5) within
		                     group (order by t.cout_terrain) as value
	                     from cout_terrain_1 t
	                                                         )
	   , cout_terrain_fixe as (
	                     select t.id as terrain_id, case when cout_fixe.use_cout_median then cout_median.value
	                                                                                    else cout_fixe.cout
	                                                end as cout_terrain
	                     from terrain_stage t
		                          join session_stage session
		                          on true
		                          join parametre_terrain_cout_affectation_fixe cout_fixe on t.id = cout_fixe.terrain_stage_id
		                          join cout_median on true
	                                                         )
	   , cout_terrain as (
	                     select *
	                     from cout_terrain_1
	                     union
	                     select *
	                     from cout_terrain_fixe
	                                                         )
	   , cout_final as (
	                     select a.affectation_id, case
		                     when a.validee or a.pre_validee then a.cout_terrain
		                     when ct.terrain_id is not null then ct.cout_terrain
		                                                     else 0
	                                              end as cout_terrain
	                     from affectation a
		                          left join tmp_affectation tmp
		                          on a.affectation_id = tmp.affectation_id
		                          left join cout_terrain ct on tmp.terrain_stage_id = ct.terrain_id
	                                                         )
	update tmp_affectation tmp
	set cout_terrain = c.cout_terrain from cout_final c
	where tmp.affectation_id = c.affectation_id;

	with affectation as (select tmp.ordre_affectation   as ordre_affectation,
		                     tmp.affectation_id      as affectation_id,
		                     tmp.terrain_stage_id as terrain_id,
		                     s.score_pre_affectation as score_pre_affectation,
		                     tmp.cout_terrain        as cout_terrain
	                     from tmp_affectation tmp
		                          join affectation_stage a on tmp.affectation_id = a.id
		                          join stage s on a.stage_id = s.id
	                     where s.session_stage_id = sessionId)

--     Calcul et maj des bonus/malus
-- /** TODO : a vérifier
--  * Régle de calcul : du bonus malus (avec R le cout du terrain affecté
--  * R * facteur correcteur du bonus/malus * (moyenne scores de cette procédure - précédent score de l'étudiant) / écart-type des score précédent des étudiants
--  **/
	   , stat_cout as (select avg(cout_terrain) as cout_moyen,
				                   percentile_disc(0.5) within
			                   group (order by cout_terrain) as cout_median,
		                   stddev_pop(cout_terrain) as ecart_type
	                   from affectation a where a.terrain_id is not null -- on ne prend pas les stages sans affectation trouvé
	                                                         )
	   , bonus_malus as (
	                     select a.affectation_id, a.terrain_id, case
		                     when terrain_id = 64 then 0 -- cas de la MG : pas de bonus/malus todo : en faire un paramétre
		                     when stat_cout.ecart_type is null or stat_cout.ecart_type=0 then -- pas de division par 0
			                     round(cast (a.cout_terrain * cast(facteur_correcteur.value as numeric) *
				                     (stat_cout.cout_moyen - a.score_pre_affectation) /1 as numeric), 2)
		                                                            else
			                     round(cast (a.cout_terrain * cast(facteur_correcteur.value as numeric) *
				                     (stat_cout.cout_moyen - a.score_pre_affectation) / stat_cout.ecart_type as numeric), 2)
	                                                            end as bonus_malus
	                     from affectation a
		                          join parametre facteur_correcteur on facteur_correcteur.code='facteur_correcteur_bonus_malus'
		                          join stat_cout
		                          on true
	                                                         )
	   , bonus_malus_final as (
	                     select a.id as affectation_id, case
		                     when a.validee or a.pre_validee then a.cout_terrain
		                     when tmp.rang_preference = 0 or tmp.terrain_stage_id is null then 0
		                                                    else least(tmp.cout_terrain + bm.bonus_malus, cast(borne_max.value as integer))
	                                                    end as cout, case
		                     when a.validee or a.pre_validee then a.bonus_malus
		                     when tmp.rang_preference = 0 or tmp.terrain_stage_id is null then 0
		                                                                 else bm.bonus_malus
	                                                                 end as bonus_malus
	                     from tmp_affectation tmp
		                          join affectation_stage a
		                          join parametre borne_max on borne_max.code = 'cout_total_max'
		                          on tmp.affectation_id = a.id
		                          left join bonus_malus bm on a.id = bm.affectation_id
	                                                         )
	update tmp_affectation tmp
	set cout       = bmf.cout,
		bonus_malus=bmf.bonus_malus from bonus_malus_final bmf
	where tmp.affectation_id = bmf.affectation_id;

-- maj des affectations
	update affectation_stage a
	set terrain_stage_id          = tmp.terrain_stage_id,
		terrain_stage_secondaire_id          = tmp.terrain_stage_secondaire_id,
		cout                         = tmp.cout,
		cout_terrain                 = tmp.cout_terrain,
		bonus_malus                  = tmp.bonus_malus,
		rang_preference              = case
			when (a.pre_validee or a.validee) then a.rang_preference
			when tmp.rang_preference = 0 then null
			                                  else tmp.rang_preference
		                               end,
		informations_complementaires =
			case
				when (not (a.pre_validee) and not (a.validee)) then 'Affectation définie par procédure automatique'
				when tmp.rang_preference = 0
				                                               then 'La procédure d''affectation automatique n''as pas trouvée d''affectation satisfaisante. Les préférences sont non-représentatives'
				                                               else null
			end
	-- pour les test de vérification
	from tmp_affectation tmp
	where a.id = tmp.affectation_id;
end;
$procedure$