SELECT
        CASE
            WHEN p.code::text = 'categorie'::text THEN cat.libelle::text
            WHEN p.code::text = 'terrain'::text THEN t.libelle::text
            WHEN p.code::text = 'general'::text THEN c.libelle::text
            ELSE 'Portée indeterminée, contrainte #'::text || c.id
        END AS "Contrainte",
    e.num_etu AS "Numéro Etudiant",
    e.nom,
    e.prenom,
    e.email,
    ce.reste_a_satisfaire AS "Nombre de stage restant à effectuer"
   FROM etudiant e
     JOIN etudiant_groupe_linker gl ON e.id = gl.etudiant_id
     JOIN groupe g ON g.id = gl.groupe_id
     JOIN contrainte_cursus_etudiant ce ON ce.etudiant_id = e.id
     JOIN contrainte_cursus c ON c.id = ce.contrainte_id
     JOIN contrainte_cursus_portee p ON p.id = c.portee
     LEFT JOIN terrain_stage t ON t.id = c.terrain_stage_id
     LEFT JOIN categorie_stage cat ON cat.id = c.categorie_stage_id
     JOIN annee_universitaire a ON a.id = g.annee_universitaire_id
     JOIN niveau_etude n ON n.id = g.niveau_etude_id
  WHERE a.date_debut < now() AND now() < a.date_fin AND n.ordre = (( SELECT max(niveau_etude.ordre) AS max
           FROM niveau_etude)) AND ce.active = true AND ce.is_sat = false AND ce.reste_a_satisfaire > 0 AND ce.validee_commission = false
  ORDER BY cat.ordre, e.nom, e.prenom, e.num_etu