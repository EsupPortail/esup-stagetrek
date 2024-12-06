-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

CREATE TABLE IF NOT EXISTS "validation_stage" (
	id bigserial PRIMARY KEY,
	stage_id bigint NOT NULL,
	is_valide boolean NOT NULL DEFAULT FALSE,
	is_invalide boolean NOT NULL DEFAULT FALSE,
	date_validation timestamp DEFAULT NULL,
	date_validation_update timestamp DEFAULT NULL,
	commentaire text DEFAULT NULL,
	commentaire_cache text DEFAULT NULL,
	warning boolean NOT NULL DEFAULT FALSE, --Permet de signaler probléme sur le déroulement du stage
	validate_by varchar(255) DEFAULT NULL,
	updated_by varchar(255) DEFAULT NULL,
	FOREIGN KEY ( stage_id )
		REFERENCES "stage" ( id )
		ON DELETE CASCADE
);


-- -------------------------
-- -- Etats ----------------
-- -------------------------
CREATE TABLE IF NOT EXISTS validation_stage_etat_linker (
	validation_stage_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (validation_stage_id, etat_instance_id),
	FOREIGN KEY (validation_stage_id) REFERENCES validation_stage (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);


INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('validation_stage','Validation des stages','fas fa-check-double','#006400','13')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;

with categorie as (
	select * from unicaen_etat_categorie
	where code = 'validation_stage'
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
	select 'valide' as code, 'Stage validé' as libelle, 'fas fa-check-double'   as icone, 'success'  as code_couleur, 1  as ordre
	union select 'invalide', 'Stage non validé', 'fas fa-times', 'danger', 2
	union select 'futur' , 'Future' , 'fas fa-clock', 'muted' , 3
--
	union select 'en_attente', 'En attente de validation', 'far fa-hourglass-half', 'primary', 4
	union select 'en_retard', 'Validation non effectuée', 'far fa-hourglass-half', 'warning', 5

	union select 'stage_non_effectue' , 'Stage non effectué' , 'fas fa-ban', 'muted' , 6
	union select 'en_disponibilite' , 'Étudiant en disponibilité' , 'fas fa-pause', 'muted' , 7

	union select 'en_alerte', 'Validation en alerte', 'fas fa-triangle-exclamation', 'warning', 8
	union select 'en_erreur', 'Validation en erreur', 'fas fa-triangle-exclamation', 'danger', 9
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


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - stage
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'validation_stage_afficher', 'Afficher la validation d''un stage', 41
	UNION
	SELECT 'validation_stage_modifier', 'Modifier l''état de validation d''un stage', 42
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'stage'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'validation_stage_afficher', 'Admin_tech'
	UNION
	SELECT 'validation_stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'validation_stage_afficher', 'Scolarite'
	UNION
	SELECT 'validation_stage_afficher', 'Garde'
	UNION
	SELECT 'validation_stage_modifier', 'Admin_tech'
	UNION
	SELECT 'validation_stage_modifier', 'Admin_fonc'
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