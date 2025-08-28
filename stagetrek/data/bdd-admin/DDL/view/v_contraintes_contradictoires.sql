WITH dates AS (
         SELECT row_number() OVER (ORDER BY tmp.date) AS num,
            tmp.date
           FROM ( SELECT DISTINCT contrainte_cursus.date_debut AS date
                   FROM contrainte_cursus
                UNION
                 SELECT DISTINCT contrainte_cursus.date_fin AS date
                   FROM contrainte_cursus) tmp
        ), periodes AS (
         SELECT row_number() OVER (ORDER BY d1.date) AS id,
            d1.date AS date_debut,
            d2.date AS date_fin
           FROM dates d1
             LEFT JOIN dates d2 ON (d1.num + 1) = d2.num
        ), contraintes_periodes AS (
         SELECT c_1.id AS contrainte_id,
            p.id AS periode_id
           FROM contrainte_cursus c_1,
            periodes p
          WHERE NOT c_1.date_fin <= p.date_debut AND NOT c_1.date_debut >= p.date_fin
        ), contraintes AS (
         SELECT c_1.id AS contrainte_id,
            cp.periode_id,
            c_1.portee,
                CASE
                    WHEN t.categorie_stage_id IS NOT NULL THEN t.categorie_stage_id::bigint
                    ELSE c_1.categorie_stage_id
                END AS categorie_id,
            c_1.terrain_stage_id AS terrain_id,
            COALESCE(c_1.nombre_de_stage_min, 0) AS min,
            COALESCE(c_1.nombre_de_stage_max, 999) AS max
           FROM contrainte_cursus c_1
             LEFT JOIN terrain_stage t ON t.id = c_1.terrain_stage_id
             JOIN contraintes_periodes cp ON cp.contrainte_id = c_1.id
        ), tuples AS (
         SELECT c1.periode_id,
            c1.contrainte_id AS c1_id,
            c2.contrainte_id AS c2_id,
            c1.portee AS c1_portee,
            c2.portee AS c2_portee,
            c1.categorie_id AS c1_categorie_id,
            c2.categorie_id AS c2_categorie_id,
            c1.terrain_id AS c1_terrain_id,
            c2.terrain_id AS c2_terrain_id,
            c1.min AS c1_min,
            c2.min AS c2_min,
            c1.max AS c1_max,
            c2.max AS c2_max
           FROM contraintes c1,
            contraintes c2
          WHERE c1.periode_id = c2.periode_id AND c1.contrainte_id < c2.contrainte_id AND c1.portee = c2.portee AND (c1.portee = 1 OR c1.portee = 2 AND c1.categorie_id = c2.categorie_id OR c1.portee = 3 AND c1.terrain_id = c2.terrain_id)
        ), contradiction_direct AS (
         SELECT tuples.periode_id,
            tuples.c1_id,
            tuples.c2_id,
            tuples.c1_min,
            tuples.c1_max,
            tuples.c2_min,
            tuples.c2_max,
            tuples.c1_min > tuples.c2_max OR tuples.c2_min > tuples.c1_max AS contradiction
           FROM tuples
        ), contraintes_terrains AS (
         SELECT p.id AS periode_id,
            t.categorie_stage_id AS categorie_id,
            t.id AS terrain_id,
            COALESCE(c_1.min, 0) AS min,
            COALESCE(c_1.max, 999) AS max
           FROM periodes p
             JOIN terrain_stage t ON true
             LEFT JOIN contraintes c_1 ON c_1.periode_id = p.id AND c_1.terrain_id = t.id
        ), bornes_categories_from_terrains AS (
         SELECT contraintes_terrains.periode_id,
            contraintes_terrains.categorie_id,
            sum(contraintes_terrains.min) AS min,
            sum(contraintes_terrains.max) AS max
           FROM contraintes_terrains
          GROUP BY contraintes_terrains.categorie_id, contraintes_terrains.periode_id
        ), bornes_categories_from_categorie AS (
         SELECT p.id AS periode_id,
            cat.id AS categorie_id,
            max(COALESCE(c_1.min, 0)) AS min,
            min(COALESCE(c_1.max, 999)) AS max
           FROM periodes p
             JOIN categorie_stage cat ON true
             LEFT JOIN contraintes c_1 ON c_1.portee = 2 AND c_1.categorie_id = cat.id AND c_1.periode_id = p.id
          GROUP BY p.id, cat.id
        ), categorie_contradiction_indirect AS (
         SELECT cc.periode_id,
            cc.categorie_id,
            cc.min AS min_by_cat,
            cc.max AS max_by_cat,
            ct.min AS min_by_ter,
            ct.max AS max_by_ter,
            ct.max < cc.min OR cc.max < ct.min OR ct.max < ct.min OR cc.max < cc.min AS contradiction
           FROM bornes_categories_from_categorie cc
             JOIN bornes_categories_from_terrains ct ON cc.periode_id = ct.periode_id AND cc.categorie_id = ct.categorie_id
        ), bornes_global_by_categories AS (
         SELECT cc.periode_id,
            sum(GREATEST(COALESCE(cc.min, 0)::bigint, COALESCE(ct.min, 0::bigint))) AS min,
            sum(GREATEST(COALESCE(cc.max, 999)::bigint, COALESCE(ct.max, 999::bigint))) AS max
           FROM bornes_categories_from_categorie cc
             JOIN bornes_categories_from_terrains ct ON cc.periode_id = ct.periode_id AND cc.categorie_id = ct.categorie_id
          GROUP BY cc.periode_id
        ), bornes_global_by_contraintes_g AS (
         SELECT p.id AS periode_id,
            max(COALESCE(c_1.min, 0)) AS min,
            min(COALESCE(c_1.max, 999)) AS max
           FROM periodes p
             LEFT JOIN contraintes c_1 ON p.id = c_1.periode_id AND c_1.portee = 1
          GROUP BY p.id
        ), contradiction_global_indirect AS (
         SELECT cc.periode_id,
            cg.min AS min_by_g,
            cg.max AS max_by_g,
            cc.min AS min_by_cat,
            cc.max AS max_by_cat,
            cg.max::numeric < cc.min OR cc.max < cg.min::numeric OR cg.max < cg.min OR cc.max < cg.min::numeric AS contradiction
           FROM bornes_global_by_contraintes_g cg
             JOIN bornes_global_by_categories cc ON cc.periode_id = cg.periode_id
        ), contradiction_indirect AS (
         SELECT c_1.contrainte_id,
            c_1.periode_id,
            c_1.portee,
            c_1.categorie_id,
            c_1.terrain_id,
            c_1.min,
            c_1.max,
            cg.contradiction OR COALESCE(cc.contradiction, false) AS contradiction,
            cg.contradiction AS contradiction_general,
            cg.min_by_g,
            cg.max_by_g,
            COALESCE(cc.contradiction, false) AS contradiction_indirect_categorie,
            COALESCE(cc.min_by_cat, 0) AS min_by_cat,
            COALESCE(cc.max_by_cat, 999) AS max_by_cat,
            COALESCE(cc.min_by_ter, 0::bigint) AS min_by_ter,
            COALESCE(cc.max_by_ter, 999::bigint) AS max_by_ter
           FROM contraintes c_1
             JOIN contradiction_global_indirect cg ON cg.periode_id = c_1.periode_id
             LEFT JOIN categorie_contradiction_indirect cc ON cc.periode_id = c_1.periode_id AND c_1.categorie_id = cc.categorie_id
        ), contradiction AS (
         SELECT c_1.contrainte_id,
            c_1.portee,
            c_1.categorie_id,
            c_1.terrain_id,
            c_1.min,
            c_1.max,
            bool_or(COALESCE(cd.contradiction, false)) OR bool_or(COALESCE(cdi.contradiction, false)) AS contradiction,
            bool_or(COALESCE(cd.contradiction, false)) AS contradiction_direct,
            bool_or(COALESCE(cdi.contradiction, false)) AS contradiction_indirect
           FROM contraintes c_1
             LEFT JOIN contradiction_direct cd ON c_1.contrainte_id = cd.c1_id OR c_1.contrainte_id = cd.c2_id
             LEFT JOIN contradiction_indirect cdi ON c_1.contrainte_id = cdi.contrainte_id
          GROUP BY c_1.contrainte_id, c_1.portee, c_1.categorie_id, c_1.terrain_id, c_1.min, c_1.max
        )
 SELECT contrainte_id
   FROM contradiction c
  WHERE contradiction = true