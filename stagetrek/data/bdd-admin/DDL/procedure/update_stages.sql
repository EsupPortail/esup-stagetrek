CREATE OR REPLACE PROCEDURE public.update_stages()
 LANGUAGE plpgsql
AS $procedure$
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
$procedure$