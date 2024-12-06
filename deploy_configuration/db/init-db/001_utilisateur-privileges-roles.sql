-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

-- ----
--  UNICAEN_UTILISATEUR / PRIVILEGES
-- ----

CREATE TABLE IF NOT EXISTS unicaen_utilisateur_role (
	id bigserial PRIMARY KEY,
	role_id varchar(64) NOT NULL,
	libelle varchar(255) NOT NULL,
	description text NOT NULL,
	is_default boolean DEFAULT FALSE NOT NULL,
	is_auto boolean DEFAULT FALSE NOT NULL,
	parent_id integer,
	ldap_filter varchar(255) DEFAULT NULL :: character varying,
	accessible_exterieur boolean DEFAULT TRUE NOT NULL,
	displayed boolean DEFAULT TRUE NOT NULL,
	CONSTRAINT fk_unicaen_utilisateur_role_parent FOREIGN KEY ( parent_id )
		REFERENCES unicaen_utilisateur_role ( id )
		DEFERRABLE INITIALLY IMMEDIATE
);

CREATE UNIQUE INDEX IF NOT EXISTS un_unicaen_utilisateur_role_role_id ON unicaen_utilisateur_role ( role_id );
CREATE INDEX IF NOT EXISTS ix_unicaen_utilisateur_role_parent ON unicaen_utilisateur_role ( parent_id );

CREATE TABLE IF NOT EXISTS unicaen_utilisateur_user (
	id bigserial PRIMARY KEY,
	username varchar(255) NOT NULL,
	display_name varchar(255) NOT NULL,
	email varchar(255),
	password varchar(128) DEFAULT 'application':: character varying NOT NULL,
	state boolean DEFAULT TRUE NOT NULL,
	password_reset_token varchar(256),
	last_role_id integer,
	CONSTRAINT un_unicaen_utilisateur_user_username UNIQUE ( username ),
	CONSTRAINT un_unicaen_utilisateur_user_password_reset_token UNIQUE ( password_reset_token ),
	CONSTRAINT fk_unicaen_utilisateur_user_last_role FOREIGN KEY ( last_role_id )
		REFERENCES unicaen_utilisateur_role ( id )
		DEFERRABLE INITIALLY IMMEDIATE
);

CREATE INDEX IF NOT EXISTS ix_unicaen_utilisateur_user_last_role ON unicaen_utilisateur_user ( last_role_id );

CREATE TABLE IF NOT EXISTS unicaen_utilisateur_role_linker (
	user_id integer NOT NULL,
	role_id integer NOT NULL,
	CONSTRAINT pk_unicaen_utilisateur_role_linker PRIMARY KEY ( user_id, role_id ),
	CONSTRAINT fk_unicaen_utilisateur_role_linker_user FOREIGN KEY ( user_id )
		REFERENCES unicaen_utilisateur_user ( id )
		DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT fk_unicaen_utilisateur_role_linker_role FOREIGN KEY ( role_id )
		REFERENCES unicaen_utilisateur_role ( id )
		DEFERRABLE INITIALLY IMMEDIATE
);

CREATE INDEX IF NOT EXISTS ix_unicaen_utilisateur_role_linker_user ON unicaen_utilisateur_role_linker ( user_id );
CREATE INDEX IF NOT EXISTS ix_unicaen_utilisateur_role_linker_role ON unicaen_utilisateur_role_linker ( role_id );

CREATE TABLE IF NOT EXISTS unicaen_privilege_categorie (
	id bigserial PRIMARY KEY,
	code varchar(150) NOT NULL,
	libelle varchar(200) NOT NULL,
	namespace varchar(255),
	ordre integer DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS un_unicaen_privilege_categorie_code ON unicaen_privilege_categorie ( code );

CREATE TABLE IF NOT EXISTS unicaen_privilege_privilege (
	id bigserial PRIMARY KEY,
	categorie_id integer NOT NULL,
	code varchar(150) NOT NULL,
	libelle varchar(200) NOT NULL,
	ordre integer DEFAULT 0,
	CONSTRAINT fk_unicaen_privilege_categorie FOREIGN KEY ( categorie_id )
		REFERENCES unicaen_privilege_categorie ( id )
		DEFERRABLE INITIALLY IMMEDIATE
);

CREATE UNIQUE INDEX IF NOT EXISTS un_unicaen_privilege_code ON unicaen_privilege_privilege ( categorie_id, code );
CREATE INDEX IF NOT EXISTS ix_unicaen_privilege_categorie ON unicaen_privilege_privilege ( categorie_id );

CREATE TABLE IF NOT EXISTS unicaen_privilege_privilege_role_linker (
	role_id integer NOT NULL,
	privilege_id integer NOT NULL,
	CONSTRAINT pk_unicaen_privilege_privilege_role_linker PRIMARY KEY ( role_id, privilege_id ),
	CONSTRAINT fk_unicaen_privilege_privilege_role_linker_role FOREIGN KEY ( role_id )
		REFERENCES unicaen_utilisateur_role ( id )
		DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT fk_unicaen_privilege_privilege_role_linker_privilege FOREIGN KEY ( privilege_id )
		REFERENCES unicaen_privilege_privilege ( id )
		DEFERRABLE INITIALLY IMMEDIATE
);

CREATE INDEX IF NOT EXISTS ix_unicaen_privilege_privilege_role_linker_role ON unicaen_privilege_privilege_role_linker ( role_id );
CREATE INDEX IF NOT EXISTS ix_unicaen_privilege_privilege_role_linker_privilege ON unicaen_privilege_privilege_role_linker ( privilege_id );


-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
-- ----
-- -- Catégories
-- ----
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 201, 'utilisateur', 'Gestion des utilisateurs', 'UnicaenUtilisateur\Provider\Privilege'
	UNION
	SELECT 202, 'role', 'Gestion des rôles', 'UnicaenUtilisateur\Provider\Privilege'
	UNION
	SELECT 203, 'privilege', 'Gestion des privilèges', 'UnicaenPrivilege\Provider\Privilege'
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
-- -- Roles
-- ----
WITH roles( role_id, libelle, description, is_default, is_auto ) AS (
	SELECT 'Admin_tech', 'Administrateur·trice Technique', 'Administrateur Technique', FALSE, FALSE
	UNION
	SELECT 'Admin_fonc'
		 , 'Administrateur·trice Fonctionnel·le'
		 , 'Administrateur·trice Fonctionnel·le'
		 , FALSE
		 , FALSE
	UNION
	SELECT 'Etudiant', 'Etudiant·e', 'Etudiant·e', FALSE, TRUE
	UNION
	SELECT 'Scolarite', 'Scolarite', 'Membre du service de scolarité', FALSE, FALSE
	UNION
	SELECT 'Observateur', 'Observateur·trice', 'Observateur·trice', FALSE, FALSE
	UNION
	SELECT 'Garde', 'Stage et Garde', 'Commission chargé de l''affectation des stages', FALSE, FALSE
-- 	UNION SELECT 'EtudiantGarde',   'Etudiant·e - Stage et Garde',     'Etudiant·e membre de la Commission Stages et Gardes ', null, null, true
)
INSERT
INTO unicaen_utilisateur_role ( role_id, libelle, description, is_default, is_auto )
SELECT *
FROM roles
ON CONFLICT (role_id)
	DO UPDATE SET libelle=excluded.libelle
	            , description=excluded.description
	            , is_default=excluded.is_default
	            , is_auto=excluded.is_auto;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH data( code_categorie, code, lib, ordre ) AS (
	SELECT 'utilisateur', 'utilisateur_afficher', 'Consulter un utilisateur', 1
	UNION
	SELECT 'utilisateur', 'utilisateur_ajouter', 'Ajouter un utilisateur', 2
	UNION
	SELECT 'utilisateur', 'utilisateur_changerstatus', 'Changer le statut d''un utilisateur', 3
	UNION
	SELECT 'utilisateur', 'utilisateur_modifierrole', 'Modifier les rôles attribués à un utilisateur', 4
	UNION
	SELECT 'utilisateur', 'utilisateur_rechercher', 'Recherche d''un utilisateur', 10
	UNION
	SELECT 'role', 'role_afficher', 'Consulter les rôles', 1
	UNION
	SELECT 'role', 'role_modifier', 'Modifier un rôle', 2
	UNION
	SELECT 'role', 'role_effacer', 'Supprimer un rôle', 3
	UNION
	SELECT 'privilege', 'privilege_voir', 'Afficher les privilèges', 1
	UNION
	SELECT 'privilege', 'privilege_ajouter', 'Ajouter un privilège', 2
	UNION
	SELECT 'privilege', 'privilege_modifier', 'Modifier un privilège', 3
	UNION
	SELECT 'privilege', 'privilege_supprimer', 'Supprimer un privilège', 4
	UNION
	SELECT 'privilege', 'privilege_affecter', 'Attribuer un privilège', 5
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = d.code_categorie
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;

-- ----
-- -- Priviléges role linker
-- ----
-- Question a revoir : un admin fonctionnel peut-il ajouter/ des utilisateurs, modifier son roles ...
WITH data ( code_privilege, code_role ) AS (
	SELECT 'utilisateur_afficher', 'Admin_tech'
	UNION
	SELECT 'utilisateur_afficher', 'Admin_fonc'
	UNION
	SELECT 'utilisateur_ajouter', 'Admin_tech'
	UNION
	SELECT 'utilisateur_changerstatus', 'Admin_tech'
	UNION
	SELECT 'utilisateur_modifierrole', 'Admin_tech'
	UNION
	SELECT 'utilisateur_rechercher', 'Admin_tech'
	UNION
	SELECT 'utilisateur_rechercher', 'Admin_fonc'
	UNION
	SELECT 'role_afficher', 'Admin_tech'
	UNION
	SELECT 'role_afficher', 'Admin_fonc'
	UNION
	SELECT 'role_modifier', 'Admin_tech'
	UNION
	SELECT 'role_effacer', 'Admin_tech'
	UNION
	SELECT 'privilege_voir', 'Admin_tech'
	UNION
	SELECT 'privilege_voir', 'Admin_fonc'
	UNION
	SELECT 'privilege_ajouter', 'Admin_tech'
	UNION
	SELECT 'privilege_modifier', 'Admin_tech'
	UNION
	SELECT 'privilege_supprimer', 'Admin_tech'
	UNION
	SELECT 'privilege_affecter', 'Admin_tech'
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

INSERT INTO "unicaen_utilisateur_user" (id, username, display_name, email, password, accessible_exterieur)
VALUES
-- Utilisateur de l'application stagetrek n'en est pas vraiment 1 (aucun role/priviléges, permet surtout de gérer lorsque l'on doit enregistrer certaines actions faite automatiquement
( 1, 'stagetrek', 'StageTrek', '', 'app', false)
ON CONFLICT (id)
DO UPDATE
set username = excluded.username,
password = excluded.password,
accessible_exterieur = excluded.accessible_exterieur;