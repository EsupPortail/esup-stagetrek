WITH coef_inadequation AS (
         SELECT (100::numeric + parametre.value::numeric) / 100::numeric AS value
           FROM parametre
          WHERE parametre.code::text = 'coef_inadequation'::text
        ), capacites_globales_terrains AS (
         SELECT GREATEST(sum(t.min_place), 1::bigint) AS min,
            GREATEST(sum(t.ideal_place), 1::bigint) AS ideal,
            GREATEST(sum(t.max_place), 1::bigint) AS max
           FROM terrain_stage t
          WHERE t.terrain_principal = true
        ), dates AS (
         SELECT session_stage.date_debut_stage AS date
           FROM session_stage
        UNION
         SELECT session_stage.date_fin_stage AS date
           FROM session_stage
        ), stage AS (
         SELECT stage.id,
            stage.session_stage_id,
            stage.etudiant_id,
            stage.informations_complementaires,
            stage.stage_non_effectue,
            stage.numero_stage,
            stage.stage_precedent_id,
            stage.stage_suivant_id,
            stage.score_pre_affectation,
            stage.score_post_affectation,
            stage.ordre_affectation,
            stage.ordre_affectation_ow,
            stage.ordre_affectation_auto
           FROM public.stage
          WHERE stage.is_stage_secondaire IS FALSE
        ), periode AS (
         SELECT d1.num AS periode_id,
            d1.date AS date_debut,
            d2.date AS date_fin,
            d2.date < now() AS periode_terminee
           FROM ( SELECT DISTINCT row_number() OVER (ORDER BY dates.date) AS num,
                    dates.date
                   FROM dates) d1
             JOIN ( SELECT DISTINCT row_number() OVER (ORDER BY dates.date) AS num,
                    dates.date
                   FROM dates) d2 ON d1.num = (d2.num - 1)
        ), session_periode AS (
         SELECT session.id AS session_id,
            periode.periode_id,
            periode.date_debut,
            periode.date_fin,
            periode.periode_terminee
           FROM periode,
            session_stage session
          WHERE NOT periode.date_fin <= session.date_debut_stage AND NOT session.date_fin_stage <= periode.date_debut
        ), places_periode AS (
         SELECT periode.periode_id,
            count(DISTINCT stage.id) AS nb_stages_periode,
            count(DISTINCT a.id) FILTER (WHERE a.validee = true) AS nb_affectations_periode
           FROM periode
             JOIN session_periode sp ON sp.periode_id = periode.periode_id
             LEFT JOIN stage ON stage.session_stage_id = sp.session_id
             LEFT JOIN affectation_stage a ON a.stage_id = stage.id
          GROUP BY periode.periode_id
        ), session_parallele AS (
         SELECT sp1.session_id AS s1_id,
            sp2.session_id AS s2_id
           FROM session_periode sp1
             JOIN session_periode sp2 ON sp1.periode_id = sp2.periode_id
          WHERE sp1.session_id <> sp2.session_id
        ), places_session AS (
         SELECT session.id AS session_id,
            count(DISTINCT stage.id) AS nb_stages_session,
            count(DISTINCT a.id) FILTER (WHERE a.validee = true) AS nb_affectations_session,
            max(pp.nb_stages_periode) AS nb_stages_periode_session,
            max(pp.nb_affectations_periode) AS nb_affectations_periode_session
           FROM session_stage session
             LEFT JOIN stage ON stage.session_stage_id = session.id
             LEFT JOIN affectation_stage a ON a.stage_id = stage.id
             JOIN session_periode sp ON sp.session_id = session.id
             JOIN places_periode pp ON pp.periode_id = sp.periode_id
          GROUP BY session.id
        ), parametres_session AS (
         SELECT session.id AS session_id,
            session.date_debut_stage < now() AS affectations_terminees,
            coef.value AS coef_inadequation,
            round(ps.nb_stages_periode_session::numeric * coef.value, 0) / capacite_terrain.ideal::numeric * 100::numeric AS taux_ideal,
                CASE
                    WHEN ps.nb_stages_periode_session <> 0 OR coef.value = 0::numeric THEN round(ps.nb_stages_session::numeric * coef.value, 0) / round(ps.nb_stages_periode_session::numeric * coef.value, 0)
                    ELSE 0::numeric
                END AS taux_repartition,
            ps.nb_stages_session,
            ps.nb_stages_periode_session,
            round(ps.nb_stages_session::numeric * coef.value, 0) AS besoins_places_session,
            round(ps.nb_stages_periode_session::numeric * coef.value, 0) AS besoins_places_periode_session,
            ps.nb_affectations_session,
            ps.nb_affectations_periode_session
           FROM session_stage session
             JOIN places_session ps ON ps.session_id = session.id
             JOIN coef_inadequation coef ON true
             JOIN capacites_globales_terrains capacite_terrain ON true
        ), session_terrain_places_affecte_tmp AS (
         SELECT session.id AS session_id,
            sstl.terrain_stage_id AS terrain_id,
            sstl.nb_places_ouvertes,
            count(DISTINCT a.id) AS nb_places_pre_affectees,
            count(DISTINCT a.id) FILTER (WHERE a.validee = true) AS nb_places_affectees
           FROM session_stage session
             JOIN session_stage_terrain_linker sstl ON session.id = sstl.session_stage_id
             LEFT JOIN stage ON stage.session_stage_id = session.id
             LEFT JOIN affectation_stage a ON stage.id = a.stage_id AND a.terrain_stage_id = sstl.terrain_stage_id
             JOIN terrain_stage t ON t.id = sstl.terrain_stage_id
          WHERE t.terrain_principal = true
          GROUP BY session.id, sstl.terrain_stage_id, sstl.nb_places_ouvertes
        ), periode_terrain_places_affecte_tmp AS (
         SELECT periode.periode_id,
            session.terrain_id,
            sum(session.nb_places_affectees) AS nb_places_affectees,
            sum(session.nb_places_ouvertes) AS nb_places_ouvertes
           FROM session_periode periode
             JOIN session_terrain_places_affecte_tmp session ON periode.session_id = session.session_id
          GROUP BY periode.periode_id, session.terrain_id
        ), session_terrain_places_affectes AS (
         SELECT s1.session_id,
            s1.terrain_id,
            s1.nb_places_ouvertes,
            s1.nb_places_pre_affectees,
            s1.nb_places_affectees,
            max(place_periode_session.nb_places_ouvertes) AS nb_places_ouvertes_periode,
            max(place_periode_session.nb_places_affectees) AS nb_places_affectees_periode
           FROM session_terrain_places_affecte_tmp s1
             LEFT JOIN session_periode periode ON s1.session_id = periode.session_id
             LEFT JOIN periode_terrain_places_affecte_tmp place_periode_session ON place_periode_session.periode_id = periode.periode_id AND place_periode_session.terrain_id = s1.terrain_id
          GROUP BY s1.session_id, s1.terrain_id, s1.nb_places_ouvertes, s1.nb_places_pre_affectees, s1.nb_places_affectees
        ), session_terrain_place AS (
         SELECT sstl.session_stage_id AS session_id,
            sstl.terrain_stage_id AS terrain_id,
            t.min_place,
            t.ideal_place,
            t.max_place,
            nb_affectations.nb_places_pre_affectees,
            nb_affectations.nb_places_affectees,
            nb_affectations.nb_places_affectees_periode,
            GREATEST(t.max_place::numeric - nb_affectations.nb_places_affectees_periode, 0::numeric) AS nb_places_non_affectees_periode,
            nb_affectations.nb_places_ouvertes,
            nb_affectations.nb_places_ouvertes_periode,
            GREATEST(t.max_place - nb_affectations.nb_places_ouvertes_periode, 0::bigint) AS nb_places_non_ouverte_periode,
                CASE
                    WHEN contrainte.niveau_etude_id IS NOT NULL THEN 0::numeric
                    WHEN round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0) > t.max_place::numeric THEN round(t.max_place::numeric * ps.taux_repartition, 0)
                    WHEN round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0) < t.min_place::numeric THEN round(t.min_place::numeric * ps.taux_repartition, 0)
                    ELSE round(ps.taux_repartition * round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0), 0)
                END AS recommandations_theorique,
                CASE
                    WHEN round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0) > t.max_place::numeric THEN t.max_place::numeric
                    WHEN round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0) < t.min_place::numeric THEN t.min_place::numeric
                    ELSE round(ps.taux_ideal * t.ideal_place::numeric / 100::numeric, 0)
                END AS recommandations_theorique_terrain_periode,
            GREATEST(nb_affectations.nb_places_ouvertes_periode - t.max_place, 0::bigint) AS surplus_ouvert,
            GREATEST(nb_affectations.nb_places_affectees_periode - t.max_place::numeric, 0::numeric) AS surplus_affecte,
            contrainte.niveau_etude_id IS NOT NULL AS restriction_niveau_etude
           FROM session_stage_terrain_linker sstl
             JOIN terrain_stage t ON sstl.terrain_stage_id = t.id
             JOIN session_stage session ON sstl.session_stage_id = session.id
             JOIN session_terrain_places_affectes nb_affectations ON nb_affectations.session_id = session.id AND nb_affectations.terrain_id = t.id
             JOIN groupe g ON session.groupe_id = g.id
             LEFT JOIN contrainte_terrain_stage_niveau_etude_linker contrainte ON t.id = contrainte.terrain_stage_id AND contrainte.niveau_etude_id = g.niveau_etude_id
             JOIN parametres_session ps ON ps.session_id = session.id
          WHERE t.terrain_principal = true
          GROUP BY sstl.session_stage_id, sstl.terrain_stage_id, t.min_place, t.ideal_place, t.max_place, nb_affectations.nb_places_pre_affectees, nb_affectations.nb_places_affectees, nb_affectations.nb_places_affectees_periode, nb_affectations.nb_places_ouvertes, nb_affectations.nb_places_ouvertes_periode, contrainte.niveau_etude_id, ps.taux_ideal, ps.taux_repartition
        ), phase1 AS (
         SELECT stp.nb_places_affectees AS nb_recommandations,
            stp.max_place::numeric - stp.nb_places_affectees_periode AS reste_disponible,
            stp.session_id,
            stp.terrain_id,
            stp.min_place,
            stp.ideal_place,
            stp.max_place,
            stp.nb_places_pre_affectees,
            stp.nb_places_affectees,
            stp.nb_places_affectees_periode,
            stp.nb_places_non_affectees_periode,
            stp.nb_places_ouvertes,
            stp.nb_places_ouvertes_periode,
            stp.nb_places_non_ouverte_periode,
            stp.recommandations_theorique,
            stp.recommandations_theorique_terrain_periode,
            stp.surplus_ouvert,
            stp.surplus_affecte,
            stp.restriction_niveau_etude
           FROM session_terrain_place stp
        ), post1 AS (
         SELECT ps.session_id,
                CASE
                    WHEN ps.affectations_terminees THEN ps.nb_affectations_session::numeric
                    ELSE ps.besoins_places_session
                END AS besoin,
            sum(phase1.nb_recommandations) AS nb_recommandations,
                CASE
                    WHEN ps.affectations_terminees THEN 0::numeric
                    ELSE GREATEST(ps.besoins_places_session - sum(phase1.nb_recommandations), 0::numeric)
                END AS reste_a_ouvrir,
            sum(phase1.reste_disponible) AS reste_disponible
           FROM parametres_session ps
             JOIN phase1 ON phase1.session_id = ps.session_id
          GROUP BY ps.session_id, ps.affectations_terminees, ps.nb_affectations_session, ps.besoins_places_session
        ), phase2 AS (
         SELECT
                CASE
                    WHEN post1.reste_a_ouvrir = 0::numeric THEN phase1.nb_recommandations::numeric
                    WHEN phase1.nb_recommandations::numeric > phase1.recommandations_theorique THEN phase1.nb_recommandations::numeric
                    WHEN phase1.recommandations_theorique < phase1.reste_disponible THEN phase1.recommandations_theorique
                    ELSE LEAST(phase1.recommandations_theorique, phase1.nb_recommandations::numeric + GREATEST(phase1.reste_disponible, 0::numeric))
                END AS nb_recommandations,
            stp.session_id,
            stp.terrain_id,
            stp.min_place,
            stp.ideal_place,
            stp.max_place,
            stp.nb_places_pre_affectees,
            stp.nb_places_affectees,
            stp.nb_places_affectees_periode,
            stp.nb_places_non_affectees_periode,
            stp.nb_places_ouvertes,
            stp.nb_places_ouvertes_periode,
            stp.nb_places_non_ouverte_periode,
            stp.recommandations_theorique,
            stp.recommandations_theorique_terrain_periode,
            stp.surplus_ouvert,
            stp.surplus_affecte,
            stp.restriction_niveau_etude
           FROM session_terrain_place stp
             JOIN phase1 ON stp.session_id = phase1.session_id AND stp.terrain_id = phase1.terrain_id
             JOIN post1 ON stp.session_id = post1.session_id
        ), post2 AS (
         SELECT ps.session_id,
                CASE
                    WHEN ps.affectations_terminees THEN ps.nb_affectations_session::numeric
                    ELSE ps.besoins_places_session
                END AS besoin,
            sum(phase2.nb_recommandations) AS nb_recommandations,
                CASE
                    WHEN ps.affectations_terminees THEN 0::numeric
                    ELSE GREATEST(ps.besoins_places_session - sum(phase2.nb_recommandations), 0::numeric)
                END AS reste_a_ouvrir
           FROM parametres_session ps
             JOIN phase2 ON phase2.session_id = ps.session_id
          GROUP BY ps.session_id, ps.affectations_terminees, ps.nb_affectations_session, ps.besoins_places_session
        ), session_incomplete AS (
         SELECT post2.session_id,
            post2.besoin,
            post2.nb_recommandations,
            post2.reste_a_ouvrir
           FROM post2
          WHERE post2.reste_a_ouvrir > 0::numeric
        ), reste_possible_terrains_tmp AS (
         SELECT session.session_id,
            p2.terrain_id,
            sp.periode_id,
            sum(p2.nb_recommandations) AS nb_recommandation_periode
           FROM session_incomplete session
             JOIN session_periode sp ON sp.session_id = session.session_id
             JOIN session_periode sp2 ON sp2.periode_id = sp.periode_id
             JOIN phase2 p2 ON sp2.session_id = p2.session_id
          GROUP BY session.session_id, sp.periode_id, p2.terrain_id
        ), reste_possible_terrains AS (
         SELECT reste_periode.session_id,
            reste_periode.terrain_id,
            t.max_place::numeric - max(reste_periode.nb_recommandation_periode) AS nb_places_restantes
           FROM reste_possible_terrains_tmp reste_periode
             JOIN session_terrain_place t ON t.terrain_id = reste_periode.terrain_id AND t.session_id = reste_periode.session_id
          WHERE t.restriction_niveau_etude IS FALSE
          GROUP BY reste_periode.session_id, reste_periode.terrain_id, t.max_place
         HAVING (t.max_place::numeric - max(reste_periode.nb_recommandation_periode)) > 0::numeric
        ), cache AS (
         SELECT reste_possible_terrains.session_id,
            reste_possible_terrains.terrain_id,
            reste_possible_terrains.nb_places_restantes
           FROM reste_possible_terrains
        ), phase3 AS (
         SELECT phase2.nb_recommandations + GREATEST(reste.nb_places_restantes, 0::numeric) AS nb_recommandations,
            reste.nb_places_restantes AS reste_ajoute,
            stp.session_id,
            stp.terrain_id,
            stp.min_place,
            stp.ideal_place,
            stp.max_place,
            stp.nb_places_pre_affectees,
            stp.nb_places_affectees,
            stp.nb_places_affectees_periode,
            stp.nb_places_non_affectees_periode,
            stp.nb_places_ouvertes,
            stp.nb_places_ouvertes_periode,
            stp.nb_places_non_ouverte_periode,
            stp.recommandations_theorique,
            stp.recommandations_theorique_terrain_periode,
            stp.surplus_ouvert,
            stp.surplus_affecte,
            stp.restriction_niveau_etude
           FROM session_terrain_place stp
             JOIN phase2 ON stp.session_id = phase2.session_id AND stp.terrain_id = phase2.terrain_id
             LEFT JOIN reste_possible_terrains reste ON stp.session_id = reste.session_id AND stp.terrain_id = reste.terrain_id
        ), post3 AS (
         SELECT ps.session_id,
                CASE
                    WHEN ps.affectations_terminees THEN ps.nb_affectations_session::numeric
                    ELSE ps.besoins_places_session
                END AS besoin,
            sum(phase3.nb_recommandations) AS nb_recommandations,
                CASE
                    WHEN ps.affectations_terminees THEN 0::numeric
                    ELSE GREATEST(ps.besoins_places_session - sum(phase3.nb_recommandations), 0::numeric)
                END AS reste_a_ouvrir
           FROM parametres_session ps
             JOIN phase3 ON phase3.session_id = ps.session_id
          GROUP BY ps.session_id, ps.affectations_terminees, ps.nb_affectations_session, ps.besoins_places_session
        ), recommandations_final_terrains_principaux AS (
         SELECT place.session_id,
            place.terrain_id,
            place.min_place,
            place.ideal_place,
            place.max_place,
            place.nb_places_pre_affectees,
            place.nb_places_affectees,
            place.nb_places_affectees_periode,
            place.nb_places_non_affectees_periode,
            place.nb_places_ouvertes,
            place.nb_places_ouvertes_periode,
            place.nb_places_non_ouverte_periode,
            place.recommandations_theorique,
            place.recommandations_theorique_terrain_periode,
            place.surplus_ouvert,
            place.surplus_affecte,
            place.restriction_niveau_etude,
            phase3.nb_recommandations AS recommandations
           FROM session_terrain_place place
             JOIN phase3 ON place.session_id = phase3.session_id AND place.terrain_id = phase3.terrain_id
        ), session_terrain2_places_affecte_tmp AS (
         SELECT session.id AS session_id,
            sstl.terrain_stage_id AS terrain_id,
            sstl.nb_places_ouvertes,
            count(DISTINCT a.id) AS nb_places_pre_affectees,
            count(DISTINCT a.id) FILTER (WHERE a.validee = true) AS nb_places_affectees
           FROM session_stage session
             JOIN session_stage_terrain_linker sstl ON session.id = sstl.session_stage_id
             LEFT JOIN stage ON stage.session_stage_id = session.id
             LEFT JOIN affectation_stage a ON stage.id = a.stage_id AND a.terrain_stage_secondaire_id = sstl.terrain_stage_id
             JOIN terrain_stage t ON t.id = sstl.terrain_stage_id
          WHERE t.terrain_principal = false
          GROUP BY session.id, sstl.terrain_stage_id, sstl.nb_places_ouvertes
        ), periode_terrain2_places_affecte_tmp AS (
         SELECT periode.periode_id,
            session.terrain_id,
            sum(session.nb_places_affectees) AS nb_places_affectees,
            sum(session.nb_places_ouvertes) AS nb_places_ouvertes
           FROM session_periode periode
             JOIN session_terrain2_places_affecte_tmp session ON periode.session_id = session.session_id
          GROUP BY periode.periode_id, session.terrain_id
        ), session_terrain2_places_affectes AS (
         SELECT s1.session_id,
            s1.terrain_id,
            s1.nb_places_ouvertes,
            s1.nb_places_pre_affectees,
            s1.nb_places_affectees,
            max(place_periode_session.nb_places_ouvertes) AS nb_places_ouvertes_periode,
            max(place_periode_session.nb_places_affectees) AS nb_places_affectees_periode
           FROM session_terrain2_places_affecte_tmp s1
             LEFT JOIN session_periode periode ON s1.session_id = periode.session_id
             LEFT JOIN periode_terrain2_places_affecte_tmp place_periode_session ON place_periode_session.periode_id = periode.periode_id AND place_periode_session.terrain_id = s1.terrain_id
          GROUP BY s1.session_id, s1.terrain_id, s1.nb_places_ouvertes, s1.nb_places_pre_affectees, s1.nb_places_affectees
        ), session_terrain2_place AS (
         SELECT sstl.session_stage_id AS session_id,
            sstl.terrain_stage_id AS terrain_id,
            t.min_place,
            t.ideal_place,
            t.max_place,
            nb_affectations.nb_places_pre_affectees,
            nb_affectations.nb_places_affectees,
            nb_affectations.nb_places_affectees_periode,
            GREATEST(t.max_place::numeric - nb_affectations.nb_places_affectees_periode, 0::numeric) AS nb_places_non_affectees_periode,
            nb_affectations.nb_places_ouvertes,
            nb_affectations.nb_places_ouvertes_periode,
            GREATEST(t.max_place - nb_affectations.nb_places_ouvertes_periode, 0::bigint) AS nb_places_non_ouverte_periode,
                CASE
                    WHEN contrainte.niveau_etude_id IS NOT NULL THEN 0
                    ELSE t.ideal_place
                END AS recommandations_theorique,
                CASE
                    WHEN contrainte.niveau_etude_id IS NOT NULL THEN GREATEST(0::bigint, nb_affectations.nb_places_affectees)
                    ELSE GREATEST(t.ideal_place::bigint, nb_affectations.nb_places_affectees)
                END AS nb_recommandations,
            GREATEST(nb_affectations.nb_places_ouvertes_periode - t.max_place, 0::bigint) AS surplus_ouvert,
            GREATEST(nb_affectations.nb_places_affectees_periode - t.max_place::numeric, 0::numeric) AS surplus_affecte,
            contrainte.niveau_etude_id IS NOT NULL AS restriction_niveau_etude
           FROM session_stage_terrain_linker sstl
             JOIN terrain_stage t ON sstl.terrain_stage_id = t.id
             JOIN session_stage session ON sstl.session_stage_id = session.id
             JOIN session_terrain2_places_affectes nb_affectations ON nb_affectations.session_id = session.id AND nb_affectations.terrain_id = t.id
             JOIN groupe g ON session.groupe_id = g.id
             LEFT JOIN contrainte_terrain_stage_niveau_etude_linker contrainte ON t.id = contrainte.terrain_stage_id AND contrainte.niveau_etude_id = g.niveau_etude_id
          WHERE t.terrain_principal = false
          GROUP BY sstl.session_stage_id, sstl.terrain_stage_id, t.min_place, t.ideal_place, t.max_place, nb_affectations.nb_places_pre_affectees, nb_affectations.nb_places_affectees, nb_affectations.nb_places_affectees_periode, nb_affectations.nb_places_ouvertes, nb_affectations.nb_places_ouvertes_periode, contrainte.niveau_etude_id
        ), recommandations_final_terrains_sedcondaires AS (
         SELECT rec2.session_id,
            rec2.terrain_id,
            rec2.min_place,
            rec2.ideal_place,
            rec2.max_place,
            rec2.nb_places_pre_affectees,
            rec2.nb_places_affectees,
            rec2.nb_places_affectees_periode,
            rec2.nb_places_non_affectees_periode,
            rec2.nb_places_ouvertes,
            rec2.nb_places_ouvertes_periode,
            rec2.nb_places_non_ouverte_periode,
            rec2.recommandations_theorique,
            rec2.nb_recommandations,
            rec2.surplus_ouvert,
            rec2.surplus_affecte,
            rec2.restriction_niveau_etude,
            rec2.nb_recommandations AS recommandations
           FROM session_terrain2_place rec2
        ), recommandations_final AS (
         SELECT recommandations_final_terrains_principaux.session_id,
            recommandations_final_terrains_principaux.terrain_id,
            recommandations_final_terrains_principaux.min_place,
            recommandations_final_terrains_principaux.ideal_place,
            recommandations_final_terrains_principaux.max_place,
            recommandations_final_terrains_principaux.nb_places_pre_affectees,
            recommandations_final_terrains_principaux.nb_places_affectees,
            recommandations_final_terrains_principaux.nb_places_affectees_periode,
            recommandations_final_terrains_principaux.nb_places_non_affectees_periode,
            recommandations_final_terrains_principaux.nb_places_ouvertes,
            recommandations_final_terrains_principaux.nb_places_ouvertes_periode,
            recommandations_final_terrains_principaux.nb_places_non_ouverte_periode,
            recommandations_final_terrains_principaux.recommandations_theorique,
            recommandations_final_terrains_principaux.recommandations_theorique_terrain_periode,
            recommandations_final_terrains_principaux.surplus_ouvert,
            recommandations_final_terrains_principaux.surplus_affecte,
            recommandations_final_terrains_principaux.restriction_niveau_etude,
            recommandations_final_terrains_principaux.recommandations
           FROM recommandations_final_terrains_principaux
        UNION
         SELECT recommandations_final_terrains_sedcondaires.session_id,
            recommandations_final_terrains_sedcondaires.terrain_id,
            recommandations_final_terrains_sedcondaires.min_place,
            recommandations_final_terrains_sedcondaires.ideal_place,
            recommandations_final_terrains_sedcondaires.max_place,
            recommandations_final_terrains_sedcondaires.nb_places_pre_affectees,
            recommandations_final_terrains_sedcondaires.nb_places_affectees,
            recommandations_final_terrains_sedcondaires.nb_places_affectees_periode,
            recommandations_final_terrains_sedcondaires.nb_places_non_affectees_periode,
            recommandations_final_terrains_sedcondaires.nb_places_ouvertes,
            recommandations_final_terrains_sedcondaires.nb_places_ouvertes_periode,
            recommandations_final_terrains_sedcondaires.nb_places_non_ouverte_periode,
            recommandations_final_terrains_sedcondaires.recommandations_theorique,
            recommandations_final_terrains_sedcondaires.nb_recommandations,
            recommandations_final_terrains_sedcondaires.surplus_ouvert,
            recommandations_final_terrains_sedcondaires.surplus_affecte,
            recommandations_final_terrains_sedcondaires.restriction_niveau_etude,
            recommandations_final_terrains_sedcondaires.recommandations
           FROM recommandations_final_terrains_sedcondaires
        )
 SELECT reco.session_id,
    reco.terrain_id,
    reco.nb_places_pre_affectees,
    reco.nb_places_affectees,
    reco.recommandations AS nb_places_recommandees,
        CASE
            WHEN reco.restriction_niveau_etude THEN 0::bigint
            ELSE reco.nb_places_non_ouverte_periode
        END AS nb_places_disponibles
   FROM recommandations_final reco
EXCEPT
 SELECT session_stage_terrain_linker.session_stage_id AS session_id,
    session_stage_terrain_linker.terrain_stage_id AS terrain_id,
    session_stage_terrain_linker.nb_places_pre_affectees,
    session_stage_terrain_linker.nb_places_affectees,
    session_stage_terrain_linker.nb_places_recommandees,
    session_stage_terrain_linker.nb_places_disponibles
   FROM session_stage_terrain_linker