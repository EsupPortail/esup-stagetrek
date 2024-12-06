-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

-- TODO : a améliorer : un champ 'contact-obligatoire' permettant de forcer le contact pour tout les terrains / Stage automatiquement
-- Idée ; gerer de maniére générique 'assistance'
CREATE TABLE IF NOT EXISTS contact (
	id bigserial PRIMARY KEY,
	code varchar(10) NOT NULL,
	libelle varchar(255) NOT NULL,
	display_name varchar(255),
	mail varchar(255),
	telephone varchar(25),
	actif boolean NOT NULL DEFAULT TRUE
);

CREATE UNIQUE INDEX IF NOT EXISTS contact_code_unique ON contact ( code );

CREATE TABLE IF NOT EXISTS contact_stage (
	id bigserial PRIMARY KEY,
	contact_id bigint NOT NULL,
	stage_id bigint NOT NULL,
	visible_par_etudiant boolean NOT NULL DEFAULT FALSE,
	is_responsable_stage boolean NOT NULL DEFAULT FALSE,
	is_signataire_convention boolean NOT NULL DEFAULT FALSE,
	priorite_ordre_signature int DEFAULT 0,
	can_valider_stage boolean NOT NULL DEFAULT FALSE,
	token_validation varchar(255) DEFAULT NULL,
	token_expiration_date timestamp DEFAULT NULL,
	send_mail_auto_liste_etudiants_stage boolean NOT NULL DEFAULT FALSE,
	send_mail_auto_validation_stage boolean NOT NULL DEFAULT FALSE,
	send_mail_auto_rappel_validation_stage boolean NOT NULL DEFAULT FALSE,
	FOREIGN KEY ( contact_id )
		REFERENCES contact ( id ),
	FOREIGN KEY ( stage_id )
		REFERENCES stage ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS contact_stage_unique ON contact_stage ( contact_id, stage_id );

-- Table utilisé pour gerer la création automatique des contact_stage lors de la validation pour la commission de ces dernier
CREATE TABLE IF NOT EXISTS contact_terrain (
	id bigserial PRIMARY KEY,
	contact_id bigint NOT NULL,
	terrain_stage_id bigint NOT NULL,
	visible_par_etudiant boolean NOT NULL DEFAULT FALSE,
	is_responsable_stage boolean NOT NULL DEFAULT FALSE,
	is_signataire_convention boolean NOT NULL DEFAULT FALSE,
	priorite_ordre_signature int DEFAULT NULL,
	can_valider_stage boolean NOT NULL DEFAULT FALSE,
	send_mail_auto_liste_etudiants_stage boolean NOT NULL DEFAULT FALSE,
	send_mail_auto_validation_stage boolean NOT NULL DEFAULT FALSE,
	send_mail_auto_rappel_validation_stage boolean NOT NULL DEFAULT FALSE,
	FOREIGN KEY ( contact_id )
		REFERENCES contact ( id ),
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS contact_terrain_unique ON contact_terrain ( contact_id, terrain_stage_id );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 5, 'contact', 'Gestion des contacts de stages', 'Application\Provider\Privilege'
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
	SELECT 'contact_afficher', 'Afficher les contacts de stages', 1
	UNION
	SELECT'contact_ajouter', 'Ajouter un contact', 2
	UNION
	SELECT 'contact_modifier', 'Modifier le contact', 3
	UNION
	SELECT 'contact_supprimer', 'Supprimer un contact', 4
	UNION
	SELECT 'contact_importer', 'Importer des contacts de stages', 5
	UNION
	SELECT 'contact_exporter', 'Exporter les contacts de stages', 6
	UNION
	SELECT 'contact_stage_afficher', 'Afficher les contacts d''un stage', 11
	UNION
	SELECT 'contact_stage_ajouter', 'Ajouter un contact à un stage', 12
	UNION
	SELECT 'contact_stage_modifier', 'Modifier le contact d''un stage', 13
	UNION
	SELECT 'contact_stage_supprimer', 'Supprimer un contact d''un stage', 14
	UNION
	SELECT 'contact_terrain_afficher', 'Afficher les contacts d''un terrain', 21
	UNION
	SELECT 'contact_terrain_ajouter', 'Ajouter un contact à un terrain', 22
	UNION
	SELECT 'contact_terrain_modifier', 'Modifier le contact d''un terrain', 23
	UNION
	SELECT 'contact_terrain_supprimer', 'Supprimer un contact d''un terrain', 24
	UNION
	SELECT 'contact_terrain_importer', 'Importer des liens entre contacts et terrains de stages', 25
	UNION
	SELECT 'contact_terrain_exporter', 'Exporter les contacts liées aux terrains de stages', 26
	UNION
	SELECT 'send_mail_validation', 'Envoyer le mail de demande validation', 31
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'contact'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'contact_afficher', 'Admin_tech'
	UNION
	SELECT 'contact_afficher', 'Admin_fonc'
	UNION
	SELECT 'contact_ajouter', 'Admin_tech'
	UNION
	SELECT 'contact_ajouter', 'Admin_fonc'
	UNION
	SELECT 'contact_modifier', 'Admin_tech'
	UNION
	SELECT 'contact_modifier', 'Admin_fonc'
	UNION
	SELECT 'contact_supprimer', 'Admin_tech'
	UNION
	SELECT 'contact_supprimer', 'Admin_fonc'
	UNION
	SELECT 'contact_importer', 'Admin_tech'
	UNION
	SELECT 'contact_importer', 'Admin_fonc'
	UNION
	SELECT 'contact_exporter', 'Admin_tech'
	UNION
	SELECT 'contact_exporter', 'Admin_fonc'
--
	UNION
	SELECT 'contact_stage_afficher', 'Admin_tech'
	UNION
	SELECT 'contact_stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'contact_stage_afficher', 'Scolarite'
	UNION
	SELECT 'contact_stage_ajouter', 'Admin_tech'
	UNION
	SELECT 'contact_stage_ajouter', 'Admin_fonc'
	UNION
	SELECT 'contact_stage_modifier', 'Admin_tech'
	UNION
	SELECT 'contact_stage_modifier', 'Admin_fonc'
	UNION
	SELECT 'contact_stage_supprimer', 'Admin_tech'
	UNION
	SELECT 'contact_stage_supprimer', 'Admin_fonc'
--
	UNION
	SELECT 'contact_terrain_afficher', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_afficher', 'Admin_fonc'
	UNION
	SELECT 'contact_terrain_ajouter', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_ajouter', 'Admin_fonc'
	UNION
	SELECT 'contact_terrain_modifier', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_modifier', 'Admin_fonc'
	UNION
	SELECT 'contact_terrain_supprimer', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_supprimer', 'Admin_fonc'
	UNION
	SELECT 'contact_terrain_importer', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_importer', 'Admin_fonc'
	UNION
	SELECT 'contact_terrain_exporter', 'Admin_tech'
	UNION
	SELECT 'contact_terrain_exporter', 'Admin_fonc'
--
	UNION
	SELECT 'send_mail_validation', 'Admin_tech'
	UNION
	SELECT 'send_mail_validation', 'Admin_fonc'
	UNION
	SELECT 'send_mail_validation', 'Scolarite'
)
   , role_linker AS (
	SELECT role.id AS role_id, privilege.id AS privilege_id, d.*
	FROM data d
		     JOIN unicaen_utilisateur_role role ON d.code_role = role.role_id
		     JOIN unicaen_privilege_privilege privilege ON d.code_privilege = privilege.code
)
   , old as ( -- Suppression des anciennes matrice
	DELETE from unicaen_privilege_privilege_role_linker l where l.privilege_id
			in (select DISTINCT privilege_id from role_linker)
)
INSERT
INTO unicaen_privilege_privilege_role_linker ( role_id, privilege_id )
	(
		SELECT role_id, privilege_id
		FROM role_linker
	)
ON CONFLICT (role_id, privilege_id) DO UPDATE -- Pour concerver ceux "supprimé mais qui doivent rester"
	set role_id = excluded.role_id, privilege_id=excluded.privilege_id ;