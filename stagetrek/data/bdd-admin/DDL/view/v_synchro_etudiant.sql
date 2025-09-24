SELECT i.num_etu,
    i.nom,
    i.prenom,
    i.email,
    i.date_naissance,
    u.id AS user_id,
    COALESCE(s.id, i.source_id) AS source_id,
    i.num_etu AS source_code
   FROM import_referentiel_etudiant i
     LEFT JOIN unicaen_utilisateur_user u ON i.email::text = u.email::text,
    source s
  WHERE s.code::text = 'stagetrek'::text