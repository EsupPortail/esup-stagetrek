CREATE or replace PROCEDURE update_contraintes_cursus()
	LANGUAGE plpgsql
AS
$$
declare
BEGIN
	-- Etape 1 : création des contraintes_cursus_etudiant manquantes
	with contraintes_etudiants as (select distinct stage.etudiant_id as etudiant_id,
	                                      contrainte.id     as contrainte_cursus_id
	                               from contrainte_cursus contrainte,
		                               stage
			                               join session_stage session on session.id = stage.session_stage_id
	                               where contrainte.date_debut <= session.date_debut_stage
		                             and session.date_fin_stage <= contrainte.date_fin)
	insert
	into contrainte_cursus_etudiant
	(etudiant_id, contrainte_id, active, validee_commission, nb_equivalences)
		(select tuple.etudiant_id          as etudiant_id,
			 tuple.contrainte_cursus_id as contrainte_id,
			 true                       as active,
			 false                      as validee_commission,
			 0                          as nb_equivalences
		 from contraintes_etudiants as tuple
			      left join contrainte_cursus_etudiant cce
			      on cce.etudiant_id = tuple.etudiant_id and cce.contrainte_id = tuple.contrainte_cursus_id
		 where cce.id is null);

-- Etape 2 : suppression des contraintes_cursus_etudiant qui ne sont plus d'actualité (changement d'une date, suppression d'un stage ...)
	with cursus_etudiant_dates as (select stage.etudiant_id                   as etudiant_id,
		                               min(session_stage.date_debut_stage) as debut_cursus_etudiant,
		                               max(session_stage.date_fin_stage)   as fin_cursus_etudiant
	                               from session_stage
		                                    join stage on session_stage.id = stage.session_stage_id
	                               group by stage.etudiant_id)
	delete
	from contrainte_cursus_etudiant
	where id in (select cce.id as contrainte_cursus_id
	             from contrainte_cursus_etudiant cce
		                  join contrainte_cursus contrainte on contrainte.id = cce.contrainte_id
		                  left join cursus_etudiant_dates on cce.etudiant_id = cursus_etudiant_dates.etudiant_id
	             where cursus_etudiant_dates.etudiant_id is null -- cas ou l'étudiant n'as pas de stage
		            or (cursus_etudiant_dates.debut_cursus_etudiant > contrainte.date_fin)
		            or (cursus_etudiant_dates.fin_cursus_etudiant < contrainte.date_debut));

-- Etape 3 : maj des contraintes contradictoire
	with to_update as (select distinct cc.id as contrainte_id,
	                          (contradiction.contrainte_id is not null) as contradictoire
	                   from contrainte_cursus cc
		                        left join v_contraintes_contradictoires contradiction on contradiction.contrainte_id = cc.id
	)
	update contrainte_cursus
	set is_contradictoire=u.contradictoire
	from to_update u
	where contrainte_cursus.id = u.contrainte_id;
end;
$$;