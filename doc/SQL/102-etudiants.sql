-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS etudiant (
	id bigserial PRIMARY KEY,
	num_etu varchar(25) NOT NULL,
	user_id bigint DEFAULT null,
	nom varchar(255) NOT NULL,
	prenom varchar(255) NOT NULL,
	email varchar(255),
	date_naissance date DEFAULT NULL,
	adresse_personnelle_id bigint DEFAULT NULL,
	cursus_valide bool DEFAULT false,
	cursus_termine bool DEFAULT false,
	FOREIGN KEY ( user_id )
		REFERENCES "unicaen_utilisateur_user" ( id )
		ON DELETE set null,
	FOREIGN KEY ( adresse_personnelle_id ) REFERENCES "adresse" ( id ) ON DELETE SET NULL,
);
CREATE UNIQUE INDEX etudiant_user_id_unique
	ON etudiant ( user_id );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 1, 'etudiant', 'Gestion des étudiants', 'Application\Provider\Privilege'
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
	SELECT 'etudiant_afficher', 'Afficher les étudiants', 1
	UNION
	SELECT 'etudiant_ajouter', 'Ajouter des étudiants', 2
	UNION
	SELECT 'etudiant_modifier', 'Modifier les étudiants', 3
	UNION
	SELECT 'etudiant_supprimer', 'Supprimer des étudiants', 4
	UNION -- A revoir
	SELECT 'etudiant_own_profil_afficher', 'Afficher son profil', 5
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
	SELECT 'etudiant_afficher', 'Admin_tech'
	UNION
	SELECT 'etudiant_afficher', 'Admin_fonc'
	UNION
	SELECT 'etudiant_afficher', 'Scolarite'
	UNION
	SELECT 'etudiant_afficher', 'Garde'
	UNION
	SELECT 'etudiant_ajouter', 'Admin_tech'
	UNION
	SELECT 'etudiant_ajouter', 'Admin_fonc'
	UNION
	SELECT 'etudiant_ajouter', 'Scolarite'
	UNION
	SELECT 'etudiant_modifier', 'Admin_tech'
	UNION
	SELECT 'etudiant_modifier', 'Admin_fonc'
	UNION
	SELECT 'etudiant_modifier', 'Scolarite'
	UNION
	SELECT 'etudiant_supprimer', 'Admin_tech'
	UNION
	SELECT 'etudiant_supprimer', 'Admin_fonc'
	UNION
	SELECT 'etudiant_own_profil_afficher', 'Etudiant'
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

-- -------------------------
-- -- Etats ----------------
-- -------------------------
CREATE TABLE IF NOT EXISTS etudiant_etat_linker (
	etudiant_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (etudiant_id, etat_instance_id),
	FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);

INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('etudiant','Étudiant','fas fa-user','#003264CC','1')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;


with categorie as (
	select * from unicaen_etat_categorie
	where code = 'etudiant'
)
   , color as (
	select 'primary' as code, '#003264CC' as code_hexa
	union
	select 'success', '#006400'
	union
	select 'info', '#729FCF'
	union
	select 'danger', '#C80000'
	union
	select 'warning', '#EE6622'
	union
	select 'muted', '#A0A0A0'
)
, data as (
	select 'cursus_en_cours'  as code, 'Cursus en cours' as libelle, 'fas fa-hourglass-half' as icone, 'primary' as code_couleur ,1 as ordre
	union select 'cursus_valide', 'Cursus terminé - Validé', 'fas fa-check', 'success', 2
	union select 'cursus_invalide', 'Cursus terminé - Non validé', 'fas fa-times', 'danger', 3
	union select 'en_alerte', 'Cursus à surveiller', 'fas fa-triangle-exclamation', 'warning',4
	union select 'en_erreur', 'Erreur dans le cursus', 'fas fa-triangle-exclamation', 'danger',5
	union select 'dispo', 'En disponibilité', 'fas fa-pause', 'muted', 6
	union select 'en_construction', 'Cursus en construction', 'fas fa-cogs', 'info', 7
)
insert into unicaen_etat_type (code, libelle, categorie_id, icone, couleur, ordre)
select categorie.code||'_'||data.code, data.libelle, categorie.id, data.icone, color.code_hexa, data.ordre
from data join color on color.code = data.code_couleur
   ,  categorie
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;
