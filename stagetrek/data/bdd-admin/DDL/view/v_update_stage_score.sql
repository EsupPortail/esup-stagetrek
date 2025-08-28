WITH stage AS (
         SELECT stage.id,
            stage.etudiant_id,
            stage.session_stage_id,
            stage.numero_stage::integer AS numero_stage,
            ss.date_commission + '1 day'::interval AS date_fin_commission,
            stage.score_pre_affectation,
            stage.score_post_affectation
           FROM public.stage
             JOIN session_stage ss ON ss.id = stage.session_stage_id
          WHERE stage.is_stage_secondaire = false
        ), cout_affectation AS (
         SELECT stage.id AS stage_id,
            stage.numero_stage,
            a.id AS affectation_id,
            a.cout_terrain,
            a.bonus_malus,
                CASE
                    WHEN a.validee AND stage.date_fin_commission <= now() THEN a.cout
                    ELSE NULL::double precision
                END AS cout_affectation
           FROM stage
             LEFT JOIN affectation_stage a ON stage.id = a.stage_id
        ), calcul AS (
         WITH RECURSIVE recursion AS (
                 SELECT stage.id AS stage_id,
                    stage.etudiant_id,
                    stage.numero_stage,
                    0::double precision AS score_pre_affectation,
                    cout.cout_affectation,
                        CASE
                            WHEN stage.date_fin_commission > now() THEN NULL::double precision
                            WHEN cout.cout_affectation IS NULL THEN 0::double precision
                            ELSE cout.cout_affectation
                        END AS score_post_affectation,
                        CASE
                            WHEN cout.cout_affectation IS NULL THEN 0
                            ELSE 1
                        END AS n,
                        CASE
                            WHEN cout.cout_affectation IS NULL THEN 0::double precision
                            ELSE cout.cout_affectation
                        END AS somme_cout_affectation
                   FROM stage
                     JOIN cout_affectation cout ON cout.stage_id = stage.id
                  WHERE stage.numero_stage = 1
                UNION
                 SELECT stage.id AS stage_id,
                    stage.etudiant_id,
                    stage.numero_stage,
                    prec.score_post_affectation AS score_pre_affectation,
                    cout.cout_affectation,
                        CASE
                            WHEN stage.date_fin_commission > now() THEN NULL::double precision
                            WHEN cout.cout_affectation IS NULL THEN prec.score_post_affectation
                            WHEN prec.n < 5 THEN (prec.somme_cout_affectation + cout.cout_affectation) / (prec.n + 1)::double precision
                            ELSE (4::double precision * prec.score_post_affectation + cout.cout_affectation) / 5::double precision
                        END AS score_post_affectation,
                        CASE
                            WHEN cout.cout_affectation IS NULL THEN prec.n
                            ELSE prec.n + 1
                        END AS n,
                        CASE
                            WHEN cout.cout_affectation IS NULL THEN prec.somme_cout_affectation
                            ELSE prec.somme_cout_affectation + cout.cout_affectation
                        END AS somme_cout_affectation
                   FROM stage
                     JOIN cout_affectation cout ON cout.stage_id = stage.id
                     JOIN recursion prec ON prec.etudiant_id = stage.etudiant_id AND (prec.numero_stage + 1) = stage.numero_stage
                )
         SELECT recursion.stage_id,
            recursion.etudiant_id,
            recursion.numero_stage,
            recursion.score_pre_affectation,
            recursion.cout_affectation,
            recursion.score_post_affectation,
            recursion.n,
            recursion.somme_cout_affectation
           FROM recursion
        ), "precision" AS (
         SELECT parametre.value::integer AS value
           FROM parametre
          WHERE parametre.code::text = 'precision_cout_affectation'::text
        ), scores AS (
         SELECT stage.id AS stage_id,
            stage.session_stage_id,
            stage.etudiant_id,
            a.id AS affectation_id,
            stage.numero_stage,
            round(a.cout_terrain::numeric, "precision".value) AS cout_terrain,
            round(a.bonus_malus::numeric, "precision".value) AS bonus_malus,
            round(calcul.cout_affectation::numeric, "precision".value) AS cout_affectation,
            round(calcul.score_pre_affectation::numeric, "precision".value) AS score_pre_affectation,
            round(calcul.score_post_affectation::numeric, "precision".value) AS score_post_affectation
           FROM stage
             JOIN calcul ON calcul.stage_id = stage.id
             JOIN affectation_stage a ON a.stage_id = stage.id,
            "precision"
        )
 SELECT scores.stage_id,
    scores.score_pre_affectation,
    scores.score_post_affectation
   FROM scores
EXCEPT
 SELECT stage.id AS stage_id,
    stage.score_pre_affectation,
    stage.score_post_affectation
   FROM stage