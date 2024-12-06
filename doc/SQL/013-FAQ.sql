
-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS faq_categorie_question (
	id bigserial PRIMARY KEY, libelle varchar(255) NOT NULL, ordre int DEFAULT 0 NOT NULL );
CREATE UNIQUE INDEX IF NOT EXISTS faq_categorie_question_unique ON "faq_categorie_question" ( libelle );

CREATE TABLE IF NOT EXISTS faq (
	id bigserial PRIMARY KEY,
	faq_categorie_id int NOT NULL,
	question varchar(255) NOT NULL,
	reponse text,
	ordre int DEFAULT 0 NOT NULL,
	FOREIGN KEY ( faq_categorie_id )
		REFERENCES "faq_categorie_question" ( id )
);

CREATE TABLE IF NOT EXISTS faq_role_visibility_linker (
	faq_id bigint NOT NULL, role_id bigint NOT NULL, PRIMARY KEY ( faq_id, role_id ), FOREIGN KEY ( faq_id )
		REFERENCES faq ( id )
		ON DELETE CASCADE, FOREIGN KEY ( role_id )
		REFERENCES unicaen_utilisateur_role ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS faq_role_visibility_linker_unique ON faq_role_visibility_linker ( faq_id, role_id );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
INSERT INTO faq_categorie_question ( id, libelle, ordre )
VALUES ( 1, 'Générale', 1 )
ON CONFLICT (id) DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 113,  'faq', 'Gestion de la FAQ', 'Application\Provider\Privilege'
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
-- 	(en dehors de la page d'affichage "ouverte a tous"
	SELECT 'faq_question_afficher', 'Afficher les questions de la faq', 1
	UNION
	SELECT  'faq_question_ajouter', 'Ajouter une question à la faq', 2
	UNION
	SELECT  'faq_question_modifier', 'Modifier une question de la faq', 3
	UNION
	SELECT  'faq_question_supprimer', 'Supprimer une question de la faq', 4
	UNION
	SELECT  'faq_categorie_afficher', 'Afficher les catégories de la faq', 11
	UNION
	SELECT  'faq_categorie_ajouter', 'Ajouter une catégorie à la faq', 12
	UNION
	SELECT  'faq_categorie_modifier', 'Modifier une catégorie de la faq', 13
	UNION
	SELECT  'faq_categorie_supprimer', 'Supprimer une catégorie de la faq', 14
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'faq'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'faq_question_afficher', 'Admin_tech'
	UNION
	SELECT 'faq_question_afficher', 'Admin_fonc'
	UNION
	SELECT 'faq_question_afficher', 'Scolarite'
	UNION
	SELECT 'faq_question_ajouter', 'Admin_tech'
	UNION
	SELECT 'faq_question_ajouter', 'Admin_fonc'
	UNION
	SELECT 'faq_question_ajouter', 'Scolarite'
	UNION
	SELECT 'faq_question_modifier', 'Admin_tech'
	UNION
	SELECT 'faq_question_modifier', 'Admin_fonc'
	UNION
	SELECT 'faq_question_modifier', 'Scolarite'
	UNION
	SELECT 'faq_question_supprimer', 'Admin_tech'
	UNION
	SELECT 'faq_question_supprimer', 'Admin_tech'
	UNION
	SELECT 'faq_question_supprimer', 'Admin_fonc'
	UNION
	SELECT 'faq_question_supprimer', 'Scolarite'
--
	UNION
	SELECT 'faq_categorie_afficher', 'Admin_tech'
	UNION
	SELECT 'faq_categorie_ajouter', 'Admin_tech'
	UNION
	SELECT 'faq_categorie_modifier', 'Admin_tech'
	UNION
	SELECT 'faq_categorie_supprimer', 'Admin_tech'
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