-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS modele_convention_stage (
	id bigserial PRIMARY KEY,
	code varchar(100) NOT NULL,
	libelle varchar(100),
	description text,
	corps_template_id bigint,
	FOREIGN KEY ( corps_template_id ) REFERENCES unicaen_renderer_template ( id ) ON DELETE SET NULL,
--
	histo_creation timestamp NOT NULL DEFAULT now( ),
	histo_createur_id bigint NOT NULL,
	histo_modification timestamp,
	histo_modificateur_id bigint,
	histo_destruction timestamp,
	histo_destructeur_id bigint,
	FOREIGN KEY ( histo_createur_id )
		REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_modificateur_id )
		REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_destructeur_id )
		REFERENCES unicaen_utilisateur_user ( id ),
	CONSTRAINT modele_convention_stage_unique UNIQUE ( code )
);

ALTER TABLE terrain_stage
	ADD COLUMN IF NOT EXISTS modele_convention_stage_id bigint DEFAULT NULL;
ALTER TABLE terrain_stage
	ADD FOREIGN KEY ( modele_convention_stage_id ) REFERENCES "modele_convention_stage" ( id ) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS convention_stage (
	id bigserial PRIMARY KEY,
	stage_id bigint NOT NULL,
	fichier_id varchar(25) DEFAULT NULL,
	modele_convention_stage_id bigint DEFAULT NULL,
	rendu bigint DEFAULT NULL,
	date_update timestamp NOT NULL,
	FOREIGN KEY ( stage_id )
		REFERENCES "stage" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( fichier_id ) REFERENCES fichier_fichier ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( modele_convention_stage_id ) REFERENCES modele_convention_stage ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( rendu )
		REFERENCES "unicaen_renderer_rendu" ( id )
		ON DELETE CASCADE
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

INSERT INTO unicaen_renderer_template ( code, description, document_type, document_sujet, document_corps, document_css, namespace )
VALUES ( 'Convention-header', 'Entête des conventions de stages', 'pdf', 'Entête des conventions de stages', '<p>Test de header</p>', '', 'Convention' )
	 , ( 'Convention-footer', 'Pied de pages des conventions de stages', 'pdf', 'Pied de page conventions de stages', '<p>Test de footer</p>', '', 'Convention' )
;

-- Macro et Paramètre utiles aux convention de stage
WITH cat AS (
	SELECT *
	FROM parametre_categorie c
	WHERE c.libelle = 'Conventions de stages'
)
   , type AS (
	SELECT *
	FROM parametre_type
)
, param AS (
	SELECT 0 AS ordre, '1' AS code, ' ' AS libelle, '' AS description, '' AS value, '' AS code_parametre_type
	UNION
	SELECT 1, 'nom_chu', 'Nom du CHU', 'Nom du CHU', 'Centre Hospitalier Universitaire de Caen', 'String'
	UNION
	SELECT 2, 'nom_ufr', 'Nom de l''UFR', 'Nom de l''UFR de santé', 'UFR de Santé de Caen', 'String'
	UNION
	SELECT 3
		 , 'adresse_ufr_sante'
		 , 'Adresse de l''UFR'
		 , 'Adresse de l''UFR de santé'
		 , 'UFR SANTÉ, 2 rue des Rochambelles, 14032 CAEN Cedex (France)'
		 , 'String'
	UNION
	SELECT 4, 'tel_ufr_sante', 'Tel de l''UFR', 'Téléphonne de L''UFR de santé', '02.31.56.82.00', 'String'
	UNION
	SELECT 5, 'fax_ufr_sante', 'Fax de l''UFR', 'Fax de l''UFR de santé', '02.31.56.82.15', 'String'
	UNION
	SELECT 6, 'mail_ufr_sante', 'Mail de l''UFR', 'Mail de contact à l''UFR de santé', '', 'String'
	UNION
	SELECT 7
		 , 'doyen_ufr_sante'
		 , 'Doyen de l''UFR'
		 , 'Nom Du Doyen De L''UFR De Santé'
		 , 'Professeur Paul Milliez'
		 , 'String'
	UNION
	SELECT 8
		 , 'directeur_chu'
		 , 'Directeur Général du CHU'
		 , 'Directeur Général du Centre Hospitalier Universitaire de Caen'
		 , 'Monsieur Frédéric Varnier'
		 , 'string'
)
   , data AS (
	SELECT cat.id  AS categorie_id
		 , p.code
		 , p.libelle
		 , p.description
		 , p.value
		 , p.ordre as ordre
		 , type.id AS parametre_type_id
	FROM param p
		     JOIN type ON type.libelle = p.code_parametre_type
	   , cat
	WHERE code != '1'
)
INSERT
INTO parametre ( categorie_id, code, libelle, description, value, ordre, parametre_type_id )
	(
		SELECT categorie_id, code, libelle, description, value, ordre, parametre_type_id
		FROM data
	)
ON CONFLICT (code) DO UPDATE SET categorie_id = excluded.categorie_id
                               , libelle = excluded.libelle
                               , description = excluded.description
                               , value = excluded.value
                               , ordre = excluded.ordre
                               , parametre_type_id = excluded.parametre_type_id;


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 6, 'convention', 'Gestion des conventions de stage', 'Application\Provider\Privilege'
)
INSERT
INTO unicaen_privilege_categorie ( code, libelle, namespace, ordre )
SELECT code, libelle, namespace, ordre
FROM categories
ON CONFLICT (code)
	DO UPDATE SET libelle=excluded.libelle
	            , namespace=excluded.namespace
	            , ordre=excluded.ordre;
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'convention_afficher', 'Afficher les conventions de stages', 1
	UNION
	SELECT 'convention_televerser', 'Téléverser une convention de stage', 2
	UNION
	SELECT 'convention_generer', 'Générer une convention de stage à partir d''un modéle', 3
	UNION
	SELECT 'convention_modifier', 'Modifier le contenue d''une convention de stage', 4
	UNION
	SELECT 'convention_supprimer', 'Supprimer une convention de stage', 5
	UNION
	SELECT 'convention_telecharger', 'Télécharger une convention de stage', 6
	UNION
	SELECT 'modele_convention_afficher', 'Afficher les modéles de conventions de stages', 11
	UNION
	SELECT 'modele_convention_ajouter', 'Ajouter un modéle de convention de stage', 12
	UNION
	SELECT 'modele_convention_modifier', 'Modifier un modéle de convention de stage', 13
	UNION
	SELECT 'modele_convention_supprimer', 'Supprimer un modéle de convention de stage', 14
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'convention'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
-- 	TODO : l'autorisé aussi au étudiants quand se sera validé
	SELECT 'convention_afficher', 'Admin_tech'
	UNION
	SELECT 'convention_afficher', 'Admin_fonc'
	UNION
	SELECT 'convention_afficher', 'Scolarite'
	UNION
	SELECT 'convention_televerser', 'Admin_tech'
	UNION
	SELECT 'convention_televerser', 'Admin_fonc'
	UNION
	SELECT 'convention_televerser', 'Scolarite'
	UNION
	SELECT 'convention_generer', 'Admin_tech'
	UNION
	SELECT 'convention_generer', 'Admin_fonc'
	UNION
	SELECT 'convention_generer', 'Scolarite'
	UNION
	SELECT 'convention_modifier', 'Admin_tech'
	UNION
	SELECT 'convention_modifier', 'Scolarite'
	UNION
	SELECT 'convention_modifier', 'Admin_fonc'
	UNION
	SELECT 'convention_modifier', 'Scolarite'
	UNION
	SELECT 'convention_supprimer', 'Admin_tech'
	UNION
	SELECT 'convention_supprimer', 'Admin_fonc'
	UNION
	SELECT 'convention_supprimer', 'Scolarite'
	UNION
	SELECT 'convention_telecharger', 'Admin_tech'
	UNION
	SELECT 'convention_telecharger', 'Admin_fonc'
	UNION
	SELECT 'convention_telecharger', 'Scolarite'
	UNION
	SELECT 'modele_convention_afficher', 'Admin_tech'
	UNION
	SELECT 'modele_convention_afficher', 'Admin_fonc'
	UNION
	SELECT 'modele_convention_afficher', 'Scolarite'
	UNION
	SELECT 'modele_convention_ajouter', 'Admin_tech'
	UNION
	SELECT 'modele_convention_ajouter', 'Admin_fonc'
	UNION
	SELECT 'modele_convention_ajouter', 'Scolarite'
	UNION
	SELECT 'modele_convention_modifier', 'Admin_tech'
	UNION
	SELECT 'modele_convention_modifier', 'Admin_fonc'
	UNION
	SELECT 'modele_convention_modifier', 'Scolarite'
	UNION
	SELECT 'modele_convention_supprimer', 'Admin_tech'
	UNION
	SELECT 'modele_convention_supprimer', 'Admin_fonc'
	UNION
	SELECT 'modele_convention_supprimer', 'Scolarite'
)
   , role_linker AS (
	SELECT role.id AS role_id, privilege.id AS privilege_id, d.*
	FROM data d
		     JOIN unicaen_utilisateur_role role ON d.code_role = role.role_id
		     JOIN unicaen_privilege_privilege privilege ON d.code_privilege = privilege.code
)
   , old AS ( -- Suppression des anciennes matrice
	DELETE FROM unicaen_privilege_privilege_role_linker l WHERE l.privilege_id
			IN (
			SELECT DISTINCT privilege_id
			FROM role_linker
		)
)
INSERT
INTO unicaen_privilege_privilege_role_linker ( role_id, privilege_id )
	(
		SELECT role_id, privilege_id
		FROM role_linker
	)
ON CONFLICT (role_id, privilege_id) DO UPDATE -- Pour concerver ceux "supprimé mais qui doivent rester"
	SET role_id = excluded.role_id, privilege_id=excluded.privilege_id;


-- Vue pour la gestions des signataires (a revoir)
CREATE VIEW v_convention_stage_signataire
			( id, stage_id, etudiant_id, contact_stage_id, convention_stage_id, libelle, display_name, mail
			, ordre_affichage ) AS
	WITH etudiant AS (
		SELECT stage.id                                       AS stage_id
			 , e.id                                           AS etudiant_id
			 , NULL::numeric                                  AS contact_stage_id
			 , convention.id                                  AS convention_stage_id
			 , 'L''étudiant.e'::text                          AS libelle
			 , ( e.prenom::text || ' '::text ) || e.nom::text AS display_name
			 , e.email                                        AS mail
			 , 1                                              AS ordre_affichage
		FROM stage
			     JOIN public.etudiant e ON stage.etudiant_id = e.id
			     LEFT JOIN convention_stage convention ON convention.stage_id = stage.id
	)
	   , contacts AS (
		SELECT stage.id                                                                                                                 AS stage_id
			 , NULL::numeric                                                                                                            AS etudiant_id
			 , contact.id                                                                                                               AS contact_stage_id
			 , convention.id                                                                                                            AS convention_stage_id
			 , contact.libelle
			 , contact.display_name
			 , contact.mail
			 , 1 + row_number( )
			       OVER (PARTITION BY stage.id ORDER BY cs.priorite_ordre_signature, contact.libelle, contact.display_name, contact.id) AS ordre_affichage
		FROM stage
			     JOIN contact_stage cs ON stage.id = cs.stage_id
			     JOIN contact ON cs.contact_id = contact.id
			     LEFT JOIN convention_stage convention ON convention.stage_id = stage.id
		WHERE contact.actif AND cs.is_signataire_convention
	)
	   , signataires AS (
		SELECT etudiant.stage_id
			 , etudiant.etudiant_id
			 , etudiant.contact_stage_id
			 , etudiant.convention_stage_id
			 , etudiant.libelle
			 , etudiant.display_name
			 , etudiant.mail
			 , etudiant.ordre_affichage
		FROM etudiant
		UNION
		SELECT contacts.stage_id
			 , contacts.etudiant_id
			 , contacts.contact_stage_id
			 , contacts.convention_stage_id
			 , contacts.libelle
			 , contacts.display_name
			 , contacts.mail
			 , contacts.ordre_affichage
		FROM contacts
	)
	SELECT row_number( ) OVER () AS id
		 , signataires.stage_id
		 , signataires.etudiant_id
		 , signataires.contact_stage_id
		 , signataires.convention_stage_id
		 , signataires.libelle
		 , signataires.display_name
		 , signataires.mail
		 , signataires.ordre_affichage
	FROM signataires;