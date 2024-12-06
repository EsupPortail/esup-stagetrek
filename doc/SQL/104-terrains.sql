-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS categorie_stage (
	id bigserial PRIMARY KEY,
	code varchar(25) NOT NULL,
	libelle varchar(255) NOT NULL,
	acronyme varchar(10) NOT NULL,
	categorie_principale boolean DEFAULT TRUE,
	ordre int DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS categorie_stage_code_unique ON "categorie_stage" ( code );
CREATE UNIQUE INDEX IF NOT EXISTS categorie_stage_libelle_unique ON "categorie_stage" ( libelle );

CREATE TABLE IF NOT EXISTS "terrain_stage" (
	id bigserial PRIMARY KEY,
	categorie_stage_id int NOT NULL,
	code varchar(25) NOT NULL,
	libelle varchar(255) NOT NULL,
	service varchar(255) DEFAULT NULL,
	adresse_id bigint DEFAULT NULL,
	lien varchar(255),-- liens vers une page exterieurs
	infos text DEFAULT NULL,-- différentes informations
	terrain_principal boolean DEFAULT TRUE,
	hors_subdivision boolean DEFAULT FALSE,
--     Autoriser le terrain pour définir les futurs préférences
	preferences_autorisees boolean NOT NULL DEFAULT TRUE,
	-- Places disponibles
	min_place int DEFAULT 0 NOT NULL,
	ideal_place int DEFAULT 0 NOT NULL,
	max_place int DEFAULT 0 NOT NULL,
--
	actif boolean DEFAULT TRUE,
	FOREIGN KEY ( categorie_stage_id )
		REFERENCES "categorie_stage" ( id ),
	FOREIGN KEY ( adresse_id ) REFERENCES "adresse" ( id ) ON DELETE SET NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS terrain_stage_code_unique ON "terrain_stage" ( code );
CREATE UNIQUE INDEX IF NOT EXISTS terrain_stage_libelle_unique ON "terrain_stage" ( libelle );

-- Pour associer des terrains de stages principaux avec des terrains de stages secondaire
-- Permet de dire qu'un terrain secondaire est disponible avec un terrain principal
CREATE TABLE terrain_stage_linker (
	terrain_principal_id bigint NOT NULL,
	terrain_secondaire_id bigint NOT NULL,
	PRIMARY KEY ( terrain_principal_id, terrain_secondaire_id ),
	FOREIGN KEY ( terrain_principal_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_secondaire_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS terrain_stage_linker_unique ON terrain_stage_linker ( terrain_principal_id, terrain_secondaire_id );

-- Terrains ayant des couts d'affectations fixe
CREATE TABLE IF NOT EXISTS parametre_terrain_cout_affectation_fixe (
	id bigserial PRIMARY KEY,
	terrain_stage_id bigint NOT NULL,
	cout int DEFAULT 0,
	use_cout_median boolean NOT NULL DEFAULT FALSE, -- Mis a true, on prendra le cout médian des affectations
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS parametre_terrain_cout_affectation_fixe_terrain_unique ON "parametre_terrain_cout_affectation_fixe" ( terrain_stage_id );


-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 2, 'terrain', 'Gestion des terrains', 'Application\Provider\Privilege'
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
	SELECT 'categorie_stage_afficher', 'Afficher les catégories de stages', 1
	UNION
	SELECT 'categorie_stage_ajouter', 'Ajouter des catégories des stages', 2
	UNION
	SELECT 'categorie_stage_modifier', 'Modifier les catégories de stages', 3
	UNION
	SELECT 'categorie_stage_supprimer', 'Supprimer des catégories de stages', 4
	UNION
	SELECT 'terrain_stage_afficher', 'Afficher les terrains de stages', 11
	UNION
	SELECT 'terrain_stage_ajouter', 'Ajouter des terrains de stages', 12
	UNION
	SELECT 'terrain_stage_modifier', 'Modifier les terrains de stages ', 13
	UNION
	SELECT 'terrain_stage_supprimer', 'Supprimer des terrains de stages', 14
	UNION
	SELECT 'terrains_importer', 'Importer des données liées aux terrains de stages', 21
	UNION
	SELECT 'terrains_exporter', 'Exporter les données liées aux terrains de stages', 22
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'terrain'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'categorie_stage_afficher', 'Admin_tech'
	UNION
	SELECT 'categorie_stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'categorie_stage_ajouter', 'Admin_tech'
	UNION
	SELECT 'categorie_stage_ajouter', 'Admin_fonc'
	UNION
	SELECT 'categorie_stage_modifier', 'Admin_tech'
	UNION
	SELECT 'categorie_stage_modifier', 'Admin_fonc'
	UNION
	SELECT 'categorie_stage_supprimer', 'Admin_tech'
	UNION
	SELECT 'categorie_stage_supprimer', 'Admin_fonc'
	UNION
	SELECT 'terrain_stage_afficher', 'Admin_tech'
	UNION
	SELECT 'terrain_stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'terrain_stage_afficher', 'Scolarite'
	UNION
	SELECT 'terrain_stage_afficher', 'Garde'
	UNION
	SELECT 'terrain_stage_modifier', 'Admin_tech'
	UNION
	SELECT 'terrain_stage_modifier', 'Admin_fonc'
	UNION
	SELECT 'terrain_stage_ajouter', 'Admin_tech'
	UNION
	SELECT 'terrain_stage_ajouter', 'Admin_fonc'
	UNION
	SELECT 'terrain_stage_supprimer', 'Admin_tech'
	UNION
	SELECT 'terrain_stage_supprimer', 'Admin_fonc'
	UNION
	SELECT 'terrains_importer', 'Admin_tech'
	UNION
	SELECT 'terrains_importer', 'Admin_fonc'
	UNION
	SELECT 'terrains_exporter', 'Admin_tech'
	UNION
	SELECT 'terrains_exporter', 'Admin_fonc'
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