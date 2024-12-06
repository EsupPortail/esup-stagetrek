-- Date de MAJ 14/10/2024 ----------------------------------------------------------------------------------------------
-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS unicaen_etat_categorie (
	id serial NOT NULL
		CONSTRAINT unicaen_etat_categorie_pk PRIMARY KEY,
	code varchar(256) NOT NULL,
	libelle varchar(256) NOT NULL,
	icone varchar(256),
	couleur varchar(256),
	ordre integer
);
CREATE UNIQUE INDEX IF NOT EXISTS unicaen_etat_categorie_id_uindex ON unicaen_etat_categorie ( id );
CREATE UNIQUE INDEX IF NOT EXISTS unicaen_etat_categorie_code_unique ON unicaen_etat_categorie ( code );

CREATE TABLE IF NOT EXISTS unicaen_etat_type (
	id serial NOT NULL
		CONSTRAINT unicaen_etat_type_pk PRIMARY KEY,
	code varchar(256) NOT NULL,
	libelle varchar(256) NOT NULL,
	categorie_id integer
		CONSTRAINT unicaen_etat_type_categorie_id_fk
			REFERENCES unicaen_etat_categorie,
	icone varchar(256),
	couleur varchar(256),
	ordre integer NOT NULL DEFAULT 9999
);
CREATE UNIQUE INDEX IF NOT EXISTS unicaen_etat_type_id_uindex ON unicaen_etat_type ( id );
CREATE UNIQUE INDEX IF NOT EXISTS unicaen_etat_type_code_unique ON unicaen_etat_type ( code );

CREATE TABLE IF NOT EXISTS unicaen_etat_instance (
	id serial
		CONSTRAINT unicaen_etat_instance_pk PRIMARY KEY,
	type_id integer NOT NULL
		CONSTRAINT unicaen_etat_instance_type_id
			REFERENCES unicaen_etat_type,
	infos text DEFAULT NULL,
	histo_creation timestamp NOT NULL,
	histo_createur_id integer NOT NULL
		CONSTRAINT unicaen_content_content_user_id_fk
			REFERENCES unicaen_utilisateur_user,
	histo_modification timestamp,
	histo_modificateur_id integer
		CONSTRAINT unicaen_content_content_user_id_fk_2
			REFERENCES unicaen_utilisateur_user,
	histo_destruction timestamp,
	histo_destructeur_id integer
		CONSTRAINT unicaen_content_content_user_id_fk_3
			REFERENCES unicaen_utilisateur_user
);



-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------


WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 211, 'etat', 'Gestion des états', 'Application\Provider\Privilege'
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
	SELECT 'etat_index', 'Afficher l''index des états', 1
	UNION
	SELECT 'etat_ajouter', 'Ajouter un état', 2
	UNION
	SELECT 'etat_modifier', 'Modifier un état', 30
	UNION
	SELECT 'etat_historiser', 'Historiser/Restaurer un etat', 40
	UNION
	SELECT 'etat_detruire', 'Supprimer un état', 5
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'etat'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'etat_index', 'Admin_tech'
	UNION
	SELECT 'etat_ajouter', 'Admin_tech'
	UNION
	SELECT 'etat_modifier', 'Admin_tech'
	UNION
	SELECT 'etat_historiser', 'Admin_tech'
	UNION
	SELECT 'etat_detruire', 'Admin_tech'
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