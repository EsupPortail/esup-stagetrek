-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

CREATE TABLE if NOT EXISTS unicaen_evenement_etat (
	id serial NOT NULL CONSTRAINT unicaen_evenement_etat_pk PRIMARY KEY,
	code varchar(255) NOT NULL, libelle varchar(255) NOT NULL, description varchar(2047)
);
CREATE UNIQUE INDEX if NOT EXISTS uevenement_etat_id_uindex ON unicaen_evenement_etat ( id );
CREATE UNIQUE INDEX if NOT EXISTS uevenement_etat_code_uindex ON unicaen_evenement_etat ( code );


CREATE TABLE if NOT EXISTS unicaen_evenement_type (
	id serial NOT NULL
		CONSTRAINT pk_evenement_type PRIMARY KEY,
	code varchar(255) NOT NULL
		CONSTRAINT un_evenement_type_code UNIQUE
			DEFERRABLE INITIALLY DEFERRED,
	libelle varchar(255) NOT NULL,
	description varchar(2047),
	parametres varchar(2047),
	recursion varchar(64)
);

CREATE TABLE if NOT EXISTS unicaen_evenement_instance (
	id serial NOT NULL
		CONSTRAINT pk_evenement_instance PRIMARY KEY,
	nom varchar(255) NOT NULL,
	description varchar(1024) NOT NULL,
	type_id integer NOT NULL
		CONSTRAINT fk_evenement_instance_type
			REFERENCES unicaen_evenement_type
			DEFERRABLE,
	etat_id integer NOT NULL
		CONSTRAINT fk_evenement_instance_etat
			REFERENCES unicaen_evenement_etat
			DEFERRABLE,
	parametres text,
	mots_clefs text,
	date_creation timestamp NOT NULL,
	date_planification timestamp NOT NULL,
	date_traitement timestamp,
	log text,
	parent_id integer
		CONSTRAINT fk_evenement_instance_parent
			REFERENCES unicaen_evenement_instance
			DEFERRABLE,
	date_fin timestamp
);

CREATE INDEX if NOT EXISTS ix_evenement_instance_type ON unicaen_evenement_instance ( type_id );
CREATE INDEX if NOT EXISTS ix_evenement_instance_etat ON unicaen_evenement_instance ( etat_id );
CREATE INDEX if NOT EXISTS ix_evenement_instance_parent ON unicaen_evenement_instance ( parent_id );

CREATE TABLE if NOT EXISTS  unicaen_evenement_journal (
	id serial NOT NULL
		CONSTRAINT unicaen_evenement_journal_pk PRIMARY KEY,
	date_execution timestamp NOT NULL,
	log text,
	etat_id integer
		CONSTRAINT unicaen_evenement_journal_unicaen_evenement_etat_id_fk
			REFERENCES unicaen_evenement_etat
			ON DELETE SET NULL
);

CREATE UNIQUE INDEX if NOT EXISTS  unicaen_evenement_journal_id_uindex ON unicaen_evenement_journal ( id );

-- ------------------------------
-- -- Insertion -----------------
-- ------------------------------
WITH data ( id, code, libelle, description ) AS (
	SELECT 1, 'en_attente', 'Événement en attente de traitement', NULL
	UNION
	SELECT 2
		 , 'en_cours'
		 , 'Événement en cours de traitement'
		 , 'L''événement est en cours de traitement et sera mis à jour après celui-ci'
	UNION
	SELECT 3, 'echec', 'Événement dont le traitement a échoué', NULL
	UNION
	SELECT 4, 'succes', 'Événement dont le traitement a réussi', NULL
	UNION
	SELECT 5, 'annule', 'Événement dont le traitement a été annulé', NULL
)
INSERT
INTO unicaen_evenement_etat ( id, code, libelle, description )
SELECT *
FROM data
ON CONFLICT (code) DO UPDATE SET libelle = excluded.libelle, description = excluded.description;


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 204, 'evenementetat', 'Gestion des états d''événements', 'UnicaenEvenement\Provider\Privilege'
	UNION
	SELECT 205, 'evenementtype', 'Gestion des types d''événements', 'UnicaenEvenement\Provider\Privilege'
	UNION
	SELECT 206, 'evenementinstance', 'Gestion des événements', 'UnicaenEvenement\Provider\Privilege'
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
WITH data( code_categorie, code, lib, ordre ) AS (
	SELECT 'evenementetat', 'etat_consultation', 'État - Visualiser les états', 1
	UNION
	SELECT 'evenementetat', 'etat_ajout', 'État - Ajouter un état', 2
	UNION
	SELECT 'evenementetat', 'etat_edition', 'État - Modifier un état', 3
	UNION
	SELECT 'evenementetat', 'etat_suppression', 'État - Supprimer un état', 4
	--
	UNION
	SELECT 'evenementtype', 'type_consultation', 'Type - Visualiser les types', 1
	UNION
	SELECT 'evenementtype', 'type_ajout', 'Type - Ajouter un type', 2
	UNION
	SELECT 'evenementtype', 'type_edition', 'Type - Modifier un type', 3
	UNION
	SELECT 'evenementtype', 'type_suppression', 'Type - Supprimer un type', 4
	--
	UNION
	SELECT 'evenementinstance', 'instance_consultation', 'Instance - Visualiser les instances', 1
	UNION
	SELECT 'evenementinstance', 'instance_ajout', 'Instance - Ajouter une instance', 2
	UNION
	SELECT 'evenementinstance', 'instance_edition', 'Instance - Modifier une instance', 3
	UNION
	SELECT 'evenementinstance', 'instance_suppression', 'Instance - Supprimer une instance', 4
	UNION
	SELECT 'evenementinstance', 'instance_traitement', 'Instance - Traiter les instances en attente', 5
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = d.code_categorie
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;

-- Question a réfléchir : autorise t'on un admin fonctionnel a consulter les événement ? (a priori non)
WITH data ( code_privilege, code_role ) AS (
	SELECT 'etat_consultation', 'Admin_tech'
	UNION
	SELECT 'etat_ajout', 'Admin_tech'
	UNION
	SELECT 'etat_edition', 'Admin_tech'
	UNION
	SELECT 'etat_suppression', 'Admin_tech'
	UNION
	SELECT 'type_consultation', 'Admin_tech'
	UNION
	SELECT 'type_ajout', 'Admin_tech'
	UNION
	SELECT 'type_edition', 'Admin_tech'
	UNION
	SELECT 'type_suppression', 'Admin_tech'
	UNION
	SELECT 'instance_consultation', 'Admin_tech'
	UNION
	SELECT 'instance_ajout', 'Admin_tech'
	UNION
	SELECT 'instance_edition', 'Admin_tech'
	UNION
	SELECT 'instance_suppression', 'Admin_tech'
	UNION
	SELECT 'instance_traitement', 'Admin_tech'

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