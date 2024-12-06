-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- VIEW ---------------------
-- ------------------------------
-- Vue permettant de faire le liens entre les événement et les enités (a partir des Paramètres)
DROP VIEW IF EXISTS v_evenement_entities_linker;
CREATE VIEW v_evenement_entities_linker AS
	select evenement.id as evenement_id,
		type_event.id as type_id, type_event.code as type_code,
		etat_event.id as etat_id, etat_event.code as etat_code,
		session.id as session_id, stage.id as stage_id, etudiant.id as etudiant_id,
		contact_stage.id as contact_stage_id
	from unicaen_evenement_instance evenement
		     join unicaen_evenement_type type_event on type_event.id = evenement.type_id
		     join unicaen_evenement_etat etat_event on etat_event.id = evenement.etat_id
		     left join session_stage session on  evenement.parametres like '%"session-id":"'||session.id||'"%'
		     left join stage on  evenement.parametres like '%"stage-id":"'||stage.id||'"%'
		     left join etudiant on  evenement.parametres like '%"etudiant-id":"'||etudiant.id||'"%'
		     left join contact_stage on  evenement.parametres like '%"contact-stage-id":"'||contact_stage.id||'"%'

