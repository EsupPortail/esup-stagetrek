-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------


-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
-- Table a revoir car le nombre de stages et les groupes ne serviront probablement plus
CREATE TABLE IF NOT EXISTS "niveau_etude" (
	id bigserial PRIMARY KEY,
	libelle varchar(255) DEFAULT NULL,
	ordre int DEFAULT 0 NOT NULL,
	nb_stages int DEFAULT 1,
	niveau_etude_parent bigint DEFAULT NULL,
	active boolean DEFAULT TRUE,
	FOREIGN KEY ( niveau_etude_parent )
		REFERENCES niveau_etude ( id )
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

INSERT
	INTO niveau_etude ( id, libelle, ordre, nb_stages, niveau_etude_parent, active )
VALUES ( 1, '4ème année', 1, 4, NULL, TRUE )
	 , ( 2, '5ème année', 2, 3, 1, TRUE )
	 , ( 3, '6ème année', 3, 5, 2, TRUE )
ON CONFLICT (id) DO UPDATE
	SET libelle = excluded.libelle
	  , ordre = excluded.ordre
	  , nb_stages = excluded.nb_stages
	  , niveau_etude_parent = excluded.niveau_etude_parent
	  , active = excluded.active
;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - parametre
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'niveau_etude_afficher', 'Afficher les niveaux d''études', 31
	UNION
	SELECT 'niveau_etude_ajouter', 'Ajouter un niveau d''étude', 32
	UNION
	SELECT 'niveau_etude_modifier', 'Modifier les niveaux d''études', 33
	UNION
	SELECT 'niveau_etude_supprimer', 'Supprimer un niveau d''étude', 34
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'parametre'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'niveau_etude_afficher', 'Admin_tech'
	UNION
	SELECT 'niveau_etude_ajouter', 'Admin_tech'
	UNION
	SELECT 'niveau_etude_modifier', 'Admin_tech'
	UNION
	SELECT 'niveau_etude_supprimer', 'Admin_tech'
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