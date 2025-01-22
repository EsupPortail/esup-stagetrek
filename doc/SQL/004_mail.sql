-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS unicaen_mail_mail (
	id serial NOT NULL
		CONSTRAINT umail_pkey PRIMARY KEY,
	date_envoi timestamp NOT NULL,
	status_envoi varchar(256) NOT NULL,
	destinataires text NOT NULL,
	destinataires_initials text,
	copies text,
	sujet text,
	corps text,
	mots_clefs text,
	log text,
	unicaen_mail_mail text
);

CREATE UNIQUE INDEX IF NOT EXISTS ummail_id_uindex ON unicaen_mail_mail ( id );

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 210, 'mail', 'UnicaenMail - Gestion des mails', 'UnicaenMail\Provider\Privilege'
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
	SELECT 'mail_index', 'Afficher les mails envoyés', 1
	UNION
	SELECT 'mail_afficher', 'Afficher un mail spécifique', 2
	UNION
	SELECT'mail_reenvoi', 'Ré-envoi de mail', 3
	UNION
	SELECT 'mail_supprimer', 'Suppression d''un mail', 4
	UNION
	SELECT'mail_test', 'Envoi d''un mail de test', 5
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'mail'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'mail_index', 'Admin_tech'
	UNION
	SELECT 'mail_index', 'Admin_fonc'
	UNION
	SELECT 'mail_afficher', 'Admin_tech'
	UNION
	SELECT 'mail_afficher', 'Admin_fonc'
	UNION
	SELECT 'mail_reenvoi', 'Admin_tech'
	UNION
	SELECT 'mail_supprimer', 'Admin_tech'
	UNION
	SELECT 'mail_test', 'Admin_tech'
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