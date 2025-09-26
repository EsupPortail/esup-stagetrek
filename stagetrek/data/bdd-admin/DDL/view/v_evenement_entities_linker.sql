SELECT evenement.id AS evenement_id,
    type_event.id AS type_id,
    type_event.code AS type_code,
    etat_event.id AS etat_id,
    etat_event.code AS etat_code,
    session.id AS session_id,
    stage.id AS stage_id,
    etudiant.id AS etudiant_id,
    contact_stage.id AS contact_stage_id
   FROM unicaen_evenement_instance evenement
     JOIN unicaen_evenement_type type_event ON type_event.id = evenement.type_id
     JOIN unicaen_evenement_etat etat_event ON etat_event.id = evenement.etat_id
     LEFT JOIN session_stage session ON evenement.parametres ~~ (('%"session-id":"'::text || session.id) || '"%'::text)
     LEFT JOIN stage ON evenement.parametres ~~ (('%"stage-id":"'::text || stage.id) || '"%'::text)
     LEFT JOIN etudiant ON evenement.parametres ~~ (('%"etudiant-id":"'::text || etudiant.id) || '"%'::text)
     LEFT JOIN contact_stage ON evenement.parametres ~~ (('%"contact-stage-id":"'::text || contact_stage.id) || '"%'::text)