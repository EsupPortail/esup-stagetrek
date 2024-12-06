-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS preference (
	id bigserial PRIMARY KEY,
	stage_id bigint NOT NULL,
	terrain_stage_id bigint NOT NULL,
	terrain_stage_secondaire_id bigint,
	rang int NOT NULL DEFAULT 1,
	is_sat boolean NOT NULL DEFAULT FALSE,
	FOREIGN KEY ( stage_id )
		REFERENCES stage ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_secondaire_id ) REFERENCES terrain_stage ( id ) ON DELETE SET NULL,
--
	histo_creation timestamp NOT NULL DEFAULT now( ),
	histo_createur_id bigint NOT NULL,
	histo_modification timestamp,
	histo_modificateur_id bigint,
	histo_destruction timestamp,
	histo_destructeur_id bigint,
	FOREIGN KEY ( histo_createur_id )
		REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_modificateur_id )
		REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_destructeur_id )
		REFERENCES unicaen_utilisateur_user ( id )
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
	SELECT 'preference_afficher', 'Afficher les préférences des étudiants', 21
	UNION
	SELECT 'preference_ajouter', 'Ajouter des préférences à un étudiant', 22
	UNION
	SELECT 'preference_modifier', 'Modifer les préférences d''un étudiant', 23
	UNION
	SELECT 'preference_supprimer', 'Supprimer les préférences d''un étudiant', 24
	UNION --TODO : a revoir si réelement utile de distinguer les 2 par un priviléges
	SELECT 'etudiant_own_preferences_afficher', 'Afficher ses préférences de stage', 25
	UNION
	SELECT 'etudiant_own_preferences_ajouter', 'Ajouter ses préférences de stage', 26
	UNION
	SELECT 'etudiant_own_preferences_modifier', 'Modifier ses préférences de stage', 27
	UNION
	SELECT 'etudiant_own_preferences_supprimer', 'Supprimer ses préférences de stage', 28
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
	SELECT 'preference_afficher', 'Admin_tech'
	UNION
	SELECT 'preference_afficher', 'Admin_fonc'
	UNION
	SELECT 'preference_afficher', 'Scolarite'
	UNION
	SELECT 'preference_afficher', 'Garde'
	UNION
	SELECT 'preference_ajouter', 'Admin_tech'
	UNION
	SELECT 'preference_modifier', 'Admin_tech'
	UNION
	SELECT 'preference_supprimer', 'Admin_tech'
	UNION
	SELECT 'etudiant_own_preferences_afficher', 'Etudiant'
	UNION
	SELECT 'etudiant_own_preferences_ajouter', 'Etudiant'
	UNION
	SELECT 'etudiant_own_preferences_modifier', 'Etudiant'
	UNION
	SELECT 'etudiant_own_preferences_supprimer', 'Etudiant'
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