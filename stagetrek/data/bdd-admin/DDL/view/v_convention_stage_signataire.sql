WITH etudiant AS (
         SELECT stage.id AS stage_id,
            e.id AS etudiant_id,
            NULL::numeric AS contact_stage_id,
            convention.id AS convention_stage_id,
            'L''étudiant.e'::text AS libelle,
            (e.prenom::text || ' '::text) || e.nom::text AS display_name,
            e.email AS mail,
            1 AS ordre_affichage
           FROM stage
             JOIN public.etudiant e ON stage.etudiant_id = e.id
             LEFT JOIN convention_stage convention ON convention.stage_id = stage.id
        ), contacts AS (
         SELECT stage.id AS stage_id,
            NULL::numeric AS etudiant_id,
            contact.id AS contact_stage_id,
            convention.id AS convention_stage_id,
            contact.libelle,
            contact.display_name,
            contact.mail,
            1 + row_number() OVER (PARTITION BY stage.id ORDER BY cs.priorite_ordre_signature, contact.libelle, contact.display_name, contact.id) AS ordre_affichage
           FROM stage
             JOIN contact_stage cs ON stage.id = cs.stage_id
             JOIN contact ON cs.contact_id = contact.id
             LEFT JOIN convention_stage convention ON convention.stage_id = stage.id
          WHERE contact.actif AND cs.is_signataire_convention
        ), signataires AS (
         SELECT etudiant.stage_id,
            etudiant.etudiant_id,
            etudiant.contact_stage_id,
            etudiant.convention_stage_id,
            etudiant.libelle,
            etudiant.display_name,
            etudiant.mail,
            etudiant.ordre_affichage
           FROM etudiant
        UNION
         SELECT contacts.stage_id,
            contacts.etudiant_id,
            contacts.contact_stage_id,
            contacts.convention_stage_id,
            contacts.libelle,
            contacts.display_name,
            contacts.mail,
            contacts.ordre_affichage
           FROM contacts
        )
 SELECT row_number() OVER () AS id,
    signataires.stage_id,
    signataires.etudiant_id,
    signataires.contact_stage_id,
    signataires.convention_stage_id,
    signataires.libelle,
    signataires.display_name,
    signataires.mail,
    signataires.ordre_affichage
   FROM signataires