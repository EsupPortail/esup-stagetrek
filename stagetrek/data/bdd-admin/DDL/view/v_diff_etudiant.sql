WITH diff AS (
         SELECT COALESCE(s.source_code, d.source_code) AS source_code,
            COALESCE(s.source_id, d.source_id) AS source_id,
                CASE
                    WHEN src.synchro_insert_enabled = true AND s.source_code IS NOT NULL AND d.source_code IS NULL THEN 'insert'::text
                    WHEN src.synchro_undelete_enabled = true AND s.source_code IS NOT NULL AND d.source_code IS NOT NULL AND d.histo_destruction IS NOT NULL AND d.histo_destruction <= LOCALTIMESTAMP(0) THEN 'undelete'::text
                    WHEN src.synchro_update_enabled = true AND s.source_code IS NOT NULL AND d.source_code IS NOT NULL AND (d.histo_destruction IS NULL OR d.histo_destruction > LOCALTIMESTAMP(0)) THEN 'update'::text
                    WHEN src.synchro_delete_enabled = true AND s.source_code IS NULL AND d.source_code IS NOT NULL AND (d.histo_destruction IS NULL OR d.histo_destruction > LOCALTIMESTAMP(0)) THEN 'delete'::text
                    ELSE NULL::text
                END AS operation,
                CASE
                    WHEN d.num_etu::text <> s.num_etu::text OR d.num_etu IS NULL AND s.num_etu IS NOT NULL OR d.num_etu IS NOT NULL AND s.num_etu IS NULL THEN 1
                    ELSE 0
                END AS u_num_etu,
                CASE
                    WHEN d.nom::text <> s.nom::text OR d.nom IS NULL AND s.nom IS NOT NULL OR d.nom IS NOT NULL AND s.nom IS NULL THEN 1
                    ELSE 0
                END AS u_nom,
                CASE
                    WHEN d.prenom::text <> s.prenom::text OR d.prenom IS NULL AND s.prenom IS NOT NULL OR d.prenom IS NOT NULL AND s.prenom IS NULL THEN 1
                    ELSE 0
                END AS u_prenom,
                CASE
                    WHEN d.email::text <> s.email::text OR d.email IS NULL AND s.email IS NOT NULL OR d.email IS NOT NULL AND s.email IS NULL THEN 1
                    ELSE 0
                END AS u_email,
                CASE
                    WHEN d.date_naissance <> s.date_naissance OR d.date_naissance IS NULL AND s.date_naissance IS NOT NULL OR d.date_naissance IS NOT NULL AND s.date_naissance IS NULL THEN 1
                    ELSE 0
                END AS u_date_naissance,
                CASE
                    WHEN d.user_id <> s.user_id OR d.user_id IS NULL AND s.user_id IS NOT NULL OR d.user_id IS NOT NULL AND s.user_id IS NULL THEN 1
                    ELSE 0
                END AS u_user_id,
                CASE
                    WHEN d.source_id <> s.source_id OR d.source_id IS NULL AND s.source_id IS NOT NULL OR d.source_id IS NOT NULL AND s.source_id IS NULL THEN 1
                    ELSE 0
                END AS u_source_id,
            s.num_etu AS s_num_etu,
            s.nom AS s_nom,
            s.prenom AS s_prenom,
            s.email AS s_email,
            s.date_naissance AS s_date_naissance,
            s.user_id AS s_user_id,
            s.source_id AS s_source_id,
            d.num_etu AS d_num_etu,
            d.nom AS d_nom,
            d.prenom AS d_prenom,
            d.email AS d_email,
            d.date_naissance AS d_date_naissance,
            d.user_id AS d_user_id,
            d.source_id AS d_source_id
           FROM etudiant d
             FULL JOIN v_synchro_etudiant s ON s.source_id = d.source_id AND s.source_code::text = d.source_code::text
             JOIN source src ON src.id = COALESCE(s.source_id, d.source_id) AND src.importable = true
        )
 SELECT diff.source_code,
    diff.source_id,
    diff.operation,
    diff.u_num_etu,
    diff.u_nom,
    diff.u_prenom,
    diff.u_email,
    diff.u_date_naissance,
    diff.u_user_id,
    diff.u_source_id,
    diff.s_num_etu,
    diff.s_nom,
    diff.s_prenom,
    diff.s_email,
    diff.s_date_naissance,
    diff.s_user_id,
    diff.s_source_id,
    diff.d_num_etu,
    diff.d_nom,
    diff.d_prenom,
    diff.d_email,
    diff.d_date_naissance,
    diff.d_user_id,
    diff.d_source_id
   FROM diff
  WHERE diff.operation IS NOT NULL AND (diff.operation = 'undelete'::text OR 0 < (diff.u_num_etu + diff.u_nom + diff.u_prenom + diff.u_email + diff.u_date_naissance + diff.u_user_id + diff.u_source_id))