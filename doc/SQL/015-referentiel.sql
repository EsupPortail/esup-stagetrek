-- Date de MAJ 22/08/2024 ----------------------------------------------------------------------------------------------
-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS source (
	id bigserial PRIMARY KEY,
	code varchar(20) NOT NULL,
	libelle varchar(255) NOT NULL,
	ordre int NOT NULL DEFAULT 1,
	CONSTRAINT un_source_code UNIQUE ( code )
);

CREATE TABLE IF NOT EXISTS referentiel_promo (
	id bigserial PRIMARY KEY,
	code varchar(20) NOT NULL,
	source_id bigint NOT NULL,
	libelle varchar(255) NOT NULL,
	code_promo varchar(255) NOT NULL,
	ordre int NOT NULL DEFAULT 1,
	FOREIGN KEY ( source_id )
		REFERENCES source ( id )
		ON DELETE CASCADE,
	CONSTRAINT un_referentiel_promo_code UNIQUE ( code )
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

INSERT INTO source ( code, libelle, ordre )
VALUES ( 'stagetrek', 'StageTrek', 1 )
ON CONFLICT (code) DO UPDATE SET libelle = excluded.libelle
                               , ordre = excluded.ordre;

-- ------------------------------
-- -- PRIVILEGES ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 115,  'referentiel', 'Gestion des référentiel de données', 'Referentiel\Provider\Privilege'
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
WITH  data( code, lib, ordre ) AS (
	SELECT 'source_afficher', 'Afficher les sources', 1
	UNION
	SELECT 'source_ajouter', 'Ajouter des sources', 2
	UNION
	SELECT 'source_modifier', 'Modifier les sources', 3
	UNION
	SELECT 'source_supprimer', 'Supprimer des sources', 4
	UNION
	SELECT 'promo_afficher', 'Afficher les codes de promotions d''étudiants dans les référentiels', 11
	UNION
	SELECT 'promo_ajouter', 'Ajouter des codes de promotions d''étudiants dans les référentiels', 12
	UNION
	SELECT 'promo_modifier', 'Modifier les codes de promotions d''étudiants dans les référentiels', 13
	UNION
	SELECT 'promo_supprimer', 'Supprimer des codes de promotions d''étudiants dans les référentiels', 14
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'referentiel'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'source_afficher', 'Admin_tech'
	UNION
	SELECT 'source_ajouter', 'Admin_tech'
	UNION
	SELECT 'source_modifier', 'Admin_tech'
	UNION
	SELECT 'source_supprimer', 'Admin_tech'
--
	UNION
	SELECT 'promo_afficher', 'Admin_tech'
	UNION
	SELECT 'promo_ajouter', 'Admin_tech'
	UNION
	SELECT 'promo_modifier', 'Admin_tech'
	UNION
	SELECT 'promo_supprimer', 'Admin_tech'
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