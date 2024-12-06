-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

CREATE TABLE IF NOT EXISTS disponibilite (
	id bigserial PRIMARY KEY,
	etudiant_id bigint NOT NULL,
	date_debut date NOT NULL,
	date_fin date NOT NULL,
	informations_complementaires varchar(255) DEFAULT NULL,
	FOREIGN KEY ( etudiant_id )
		REFERENCES "etudiant" ( id )
		ON DELETE CASCADE
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - etudiant
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'disponibilite_afficher', 'Afficher les disponiblité des étudiants', 31
	UNION
	SELECT 'disponibilite_ajouter', 'Ajouter des disponiblité à un étudiant', 32
	UNION
	SELECT 'disponibilite_modifier', 'Modifer les disponiblité d''un étudiant', 33
	UNION
	SELECT 'disponibilite_supprimer', 'Supprimer les disponiblité d''un étudiant', 34
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'etudiant'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'disponibilite_afficher', 'Admin_tech'
	UNION
	SELECT 'disponibilite_afficher', 'Admin_fonc'
	UNION
	SELECT 'disponibilite_afficher', 'Scolarite'
	UNION
	SELECT 'disponibilite_afficher', 'Garde'
	UNION
	SELECT 'disponibilite_ajouter', 'Admin_tech'
	UNION
	SELECT 'disponibilite_ajouter', 'Admin_fonc'
	UNION
	SELECT 'disponibilite_ajouter', 'Scolarite'
	UNION
	SELECT 'disponibilite_modifier', 'Admin_tech'
	UNION
	SELECT 'disponibilite_modifier', 'Admin_fonc'
	UNION
	SELECT 'disponibilite_modifier', 'Scolarite'
	UNION
	SELECT 'disponibilite_supprimer', 'Admin_tech'
	UNION
	SELECT 'disponibilite_supprimer', 'Admin_fonc'
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


