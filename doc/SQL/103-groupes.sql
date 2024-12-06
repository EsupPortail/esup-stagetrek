-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE groupe (
	id bigserial PRIMARY KEY,
	libelle varchar(255) DEFAULT NULL,
	niveau_etude_id bigint NOT NULL,
	annee_universitaire_id bigint NOT NULL,
	groupe_precedent_id bigint DEFAULT NULL,
	groupe_suivant_id bigint DEFAULT NULL,
	FOREIGN KEY ( niveau_etude_id )
		REFERENCES "niveau_etude" ( id ),
	FOREIGN KEY ( annee_universitaire_id )
		REFERENCES "annee_universitaire" ( id ),
	FOREIGN KEY ( groupe_precedent_id ) REFERENCES "groupe" ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( groupe_suivant_id ) REFERENCES "groupe" ( id ) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS etudiant_groupe_linker (
	etudiant_id bigint NOT NULL,
	groupe_id bigint NOT NULL,
	PRIMARY KEY ( etudiant_id, groupe_id ),
	FOREIGN KEY ( etudiant_id )
		REFERENCES "etudiant" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( groupe_id )
		REFERENCES "groupe" ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS etudiant_groupe_linker_unique ON etudiant_groupe_linker ( etudiant_id, groupe_id );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - etudiant
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'groupe_afficher', 'Afficher les groupes d''étudiants', 11
	UNION
	SELECT 'groupe_ajouter', 'Ajouter des groupes d''étudiants', 12
	UNION
	SELECT 'groupe_modifier', 'Modifier les groupes d''étudiants', 13
	UNION
	SELECT 'groupe_supprimer', 'Supprimer des groupes d''étudiants', 14
	UNION
	SELECT 'groupe_administrer_etudiants', 'Administrer les étudiants inscrit dans un groupe', 15
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
	SELECT 'groupe_afficher', 'Admin_tech'
	union
	SELECT 'groupe_afficher', 'Admin_fonc'
	union
	SELECT 'groupe_afficher', 'Scolarite'
	union
	SELECT 'groupe_afficher', 'Garde'
	union
	SELECT 'groupe_ajouter', 'Admin_tech'
	union
	SELECT 'groupe_ajouter', 'Admin_fonc'
	union
	SELECT 'groupe_ajouter', 'Scolarite'
	union
	SELECT 'groupe_modifier', 'Admin_tech'
	union
	SELECT 'groupe_modifier', 'Admin_fonc'
	union
	SELECT 'groupe_modifier', 'Scolarite'
	union
	SELECT 'groupe_supprimer', 'Admin_tech'
	union
	SELECT 'groupe_supprimer', 'Admin_fonc'
	union
	SELECT 'groupe_supprimer', 'Scolarite'
	union
	SELECT 'groupe_administrer_etudiants', 'Admin_tech'
	union
	SELECT 'groupe_administrer_etudiants', 'Admin_fonc'
	union
	SELECT 'groupe_administrer_etudiants', 'Scolarite'
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