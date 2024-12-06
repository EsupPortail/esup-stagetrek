-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- VIEW ----------------------
-- ------------------------------

-- A voir si l'on peut supprimer la procédure
-- En attendant la garder et y faire appel au besoins
-- ------------------------------
-- -- PROCEDURE -----------------
-- ------------------------------
-- Créations automatiques des contacts de stages de stages a partir des affectations
CREATE or replace PROCEDURE update_contacts_stages()
	LANGUAGE plpgsql
AS
$$
declare

BEGIN
	-- -- Création automatique des contacts de stages de stages manquants
	with session as (select session_stage.id as session_stage_id
	                 from session_stage
--                 Si le stage a été validé/invalidé, on ne modifie pas les contact
                     where session_stage.date_commission <= now()
                   and now() <= session_stage.date_fin_validation

	)
	   , contact_to_create as (select stage.id              as stage_id,
		                           session.session_stage_id,
		                           e.num_etu,
		                           e.nom,
		                           e.prenom,
		                           a.terrain_stage_id as terrain_id,
		                           ct.contact_id         as contact_id
	                           from session
		                                join stage on stage.session_stage_id = session.session_stage_id
		                                join validation_stage v on v.stage_id = stage.id
		                                join affectation_stage a on a.stage_id = stage.id
		                                join affectation_stage_etat_linker el on a.id = el.affectation_stage_id
		                                join unicaen_etat_instance etat on el.etat_instance_id = etat.id
		                                join unicaen_etat_type etat_type on etat.type_id = etat_type.id
		                                join etudiant e on stage.etudiant_id = e.id
		                                join contact_terrain ct on ct.terrain_stage_id = a.terrain_stage_id
		                                join contact on contact.id = ct.contact_id
		                                left join contact_stage cs
		                                on (stage.id = cs.stage_id and cs.contact_id = ct.contact_id)
--                 Si le stage a été validé/invalidé, on ne modifie pas les contact
                                 where etat_type.code = 'affectation_valide'
	                             and etat.histo_destruction is null
	                             and v.is_valide = false and v.is_invalide=false
	                             and contact.actif = true
         and cs.id is null)
	insert into contact_stage (contact_id, stage_id, visible_par_etudiant, is_responsable_stage, is_signataire_convention,
	                    can_valider_stage,send_mail_auto_liste_etudiants_stage, send_mail_auto_validation_stage, send_mail_auto_rappel_validation_stage
	)
		(select contact.id as contact_id,
			 c.stage_id as stage_id,
			 ct.visible_par_etudiant,
			 ct.is_responsable_stage,
			 ct.is_signataire_convention,
			 ct.can_valider_stage,
			 ct.send_mail_auto_liste_etudiants_stage,
			 ct.send_mail_auto_validation_stage,
			 ct.send_mail_auto_rappel_validation_stage
		 from contact_to_create c
			      join contact on contact.id = c.contact_id
			      join contact_terrain ct on contact.id = ct.contact_id and ct.terrain_stage_id = c.terrain_id
		)
	on conflict (contact_id, stage_id) do nothing ;

	--     Cas spécifique de assistance : on ne le supprime jamais
-- -- Suppression de contact de stage associé a un autre terrain mais créé par erreur
	with stages as (select s.id as stage_id
	                 from stage s
		                 join session_stage on s.session_stage_id = session_stage.id
		                      join validation_stage v on v.stage_id = s.id
--                 Si le stage a été validé/invalidé, on ne modifie pas les contact
                     where v.is_valide = false and v.is_invalide=false
                 and now() < session_stage.date_debut_stage -- pour ne pas supprimer des contacts de stages qui était valide avant
	)
	delete from contact_stage where id in (
		select cs.id as contact_stage_id
		from stages s
			     join affectation_stage a on a.stage_id = s.stage_id
			     join affectation_stage_etat_linker el on a.id = el.affectation_stage_id
			     join unicaen_etat_instance etat on el.etat_instance_id = etat.id
			     join unicaen_etat_type etat_type on etat.type_id = etat_type.id
				 join contact_stage cs on s.stage_id = cs.stage_id
			     join contact on cs.contact_id = contact.id
			     left join contact_terrain ct on ct.contact_id = contact.id and
					ct.terrain_stage_id = a.terrain_stage_id
			where etat.histo_destruction is null
			and (ct.terrain_stage_id is null or etat_type.code != 'affectation_valide') -- supprime les contacts de stages correspondant au terrain mais que l'affectation n'est pas / plus valide
		  and contact.code != 'assistance'
	);

--     Ajout automatique de assistance à tout les stages tout les terrains s'il n'existe pas
	insert into contact_stage (contact_id, stage_id, visible_par_etudiant)
		(
			select contact.id, stage.id, true from stage, contact
			where contact.code = 'assistance'
		)
	on conflict (contact_id, stage_id)
		do nothing;

	insert into contact_terrain (contact_id, terrain_stage_id, visible_par_etudiant)
		(
			select contact.id, terrain_stage.id, true from terrain_stage, contact
			where contact.code = 'assistance'
		)
	on conflict (contact_id, terrain_stage_id)
		do nothing;

	-- Mise en place de assistance comme valideur pour les stages n'ayant personne pour les valider
-- with to_update as (
--     select stage.id, assistance.id as contact_stage_id
--     from stage
--              join session_stage ss on stage.session_stage_id = ss.id
--              join validation_stage vs on stage.id = vs.stage_id
--              left join contact_stage cs on (stage.id = cs.stage_id and cs.can_valider_stage = true)
--              join contact a on a.code = 'assistance'
--              join contact_stage assistance
--                   on (assistance.stage_id = stage.id and assistance.contact_id = a.id)
--     where ss.date_debut_validation < now()
--       and vs.is_valide = false
--       and vs.is_invalide = false
--       and cs.id is null
-- )
-- update contact_stage cs
-- set can_valider_stage = true,
--     send_mail_auto_validation_stage = true,
--     send_mail_auto_rappel_validation_stage = true
--     from to_update u
-- where cs.id = u.contact_stage_id;
END;
$$;
