CREATE OR REPLACE PROCEDURE public.update_sessions_stages_places()
 LANGUAGE plpgsql
AS $procedure$
BEGIN
	-- Création automatiques des sessions stage_terrain_linker manquant;
	insert into session_stage_terrain_linker (session_stage_id, terrain_stage_id)
		(select ss.id as session_id, t.id as terrain_id
		 from session_stage ss
			      join terrain_stage t on true
			      left join session_stage_terrain_linker sstl
			      on t.id = sstl.terrain_stage_id and sstl.session_stage_id = ss.id
		 where sstl.session_stage_id is null);

-- Maj des recommandations de places à ouvrir
	update session_stage_terrain_linker sstl
	set
		nb_places_pre_affectees = u.nb_places_pre_affectees,
		nb_places_affectees = u.nb_places_affectees,
		nb_places_recommandees = u.nb_places_recommandees,
		nb_places_disponibles = u.nb_places_disponibles

	from v_update_session_stage_places_terrains u
	where sstl.session_stage_id = u.session_id
	  and  sstl.terrain_stage_id = u.terrain_id;

-- Pour les sessions de stages qui n'ont pas encore été initialisé, on ouvre par défaut les recommandations
	with session_non_init as (select session.id as session_id
	                          from session_stage session
		                               join session_stage_terrain_linker sstl on session.id = sstl.session_stage_id
	                          where now() < session.date_debut_choix
	                          group by session.id
	                          having sum(sstl.nb_places_ouvertes) = 0 and sum(sstl.nb_places_recommandees)>0),
		recommandation as (select sstl.id as id, sstl.nb_places_recommandees as nb_places_recommandees
		                   from session_non_init
			                        join session_stage_terrain_linker sstl on sstl.session_stage_id = session_non_init.session_id
		                                                                                                    )
	update session_stage_terrain_linker sstl
	set nb_places_ouvertes = recommandation.nb_places_recommandees
	from recommandation
	where sstl.id = recommandation.id;
END;
$procedure$