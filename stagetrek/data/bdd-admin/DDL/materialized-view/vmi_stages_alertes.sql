SELECT a.libelle AS annee,
    g.libelle AS groupe,
    e.num_etu AS "Numéro étudiant",
    e.prenom AS "Prénom",
    e.nom AS "Nom",
    (('du '::text || to_char(ss.date_debut_stage, 'DD/MM/YYYY'::text)) || ' au '::text) || to_char(ss.date_fin_stage, 'DD/MM/YYYY'::text) AS "Période",
    s.numero_stage AS "stage numéro",
    COALESCE(et.libelle, 'Indéterminé'::character varying) AS etat,
    ei.infos AS "informations complementaires"
   FROM stage s
     JOIN stage_etat_linker el ON el.stage_id = s.id
     LEFT JOIN unicaen_etat_instance ei ON el.etat_instance_id = ei.id
     LEFT JOIN unicaen_etat_type et ON ei.type_id = et.id
     JOIN etudiant e ON e.id = s.etudiant_id
     JOIN session_stage ss ON ss.id = s.session_stage_id
     JOIN groupe g ON ss.groupe_id = g.id
     JOIN annee_universitaire a ON g.annee_universitaire_id = a.id
  WHERE (ei.id IS NULL OR ei.histo_destruction IS NULL) AND (et.id IS NULL OR (et.code::text = ANY (ARRAY['stage_en_alerte'::character varying::text, 'stage_en_erreur'::character varying::text])))
  ORDER BY et.ordre, ss.date_debut_stage DESC, e.num_etu