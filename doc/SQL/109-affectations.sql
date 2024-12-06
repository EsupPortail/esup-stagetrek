-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

CREATE TABLE IF NOT EXISTS affectation_stage (
	id bigserial PRIMARY KEY,
	stage_id bigint NOT NULL,
	terrain_stage_id bigint,
-- 	Pour le cas de doubles stages (Soins paliatifs)
	stage_secondaire_id bigint DEFAULT NULL,
	terrain_stage_secondaire_id bigint DEFAULT NULL,
	informations_complementaires text DEFAULT NULL,
-- 	Cout des entities
	cout float DEFAULT 0,
	cout_terrain float DEFAULT 0,
	bonus_malus float DEFAULT 0,
	rang_preference int DEFAULT NULL,
-- 	Distinction : Validée : publiée vers l'étudiant : pré-validée = non modifiable par la procédures d'affectation mais pas encore visible par l'étudiant
	pre_validee bool NOT NULL DEFAULT FALSE,
	validee bool NOT NULL DEFAULT FALSE,
	FOREIGN KEY ( stage_id ) REFERENCES stage ( id ) ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_id ) REFERENCES terrain_stage ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( stage_secondaire_id ) REFERENCES stage ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( terrain_stage_secondaire_id ) REFERENCES terrain_stage ( id ) ON DELETE SET NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS affectation_stage_unique ON "affectation_stage" ( stage_id );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------



-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - stage
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT  'affectation_afficher', 'Afficher les affectations de stage', 31
	UNION
	SELECT 'affectation_ajouter', 'Ajouter une affectation de stage', 32
	UNION
	SELECT  'affectation_modifier', 'Modifier une affectation de stage', 33
	UNION
	SELECT 'affectation_supprimer', 'Supprimer une affectation de stage', 34
	UNION
	SELECT  'affectation_run_procedure', 'Appliquer les procédures d''affectation de stage ', 35
	UNION
	SELECT   'affectation_pre_valider', 'Pre-valider une affectation de stage', 36
	UNION
	SELECT  'commission_valider_affectations', 'Validation des affectations de stages par la commission', 37
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'stage'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
-- ----
WITH data ( code_privilege, code_role ) AS (
-- 	Q : autorisé aussi l'administrateur fonctionnel (qui fait probablement partie de la commission ?)
	SELECT 'affectation_afficher', 'Admin_tech'
	UNION
	SELECT 'affectation_afficher', 'Admin_fonc'
	UNION
	SELECT 'affectation_afficher', 'Scolarite'
	UNION
	SELECT 'affectation_afficher', 'Garde'
	UNION
	SELECT 'affectation_ajouter', 'Admin_tech'
	UNION
	SELECT 'affectation_ajouter', 'Garde'
	UNION
	SELECT 'affectation_modifier', 'Admin_tech'
	UNION
	SELECT 'affectation_modifier', 'Garde'
	UNION
	SELECT 'affectation_supprimer', 'Admin_tech'
	UNION
	SELECT 'affectation_supprimer', 'Garde'
	UNION
	SELECT 'affectation_run_procedure', 'Admin_tech'
	UNION
	SELECT 'affectation_run_procedure', 'Garde'
	UNION
	SELECT 'affectation_pre_valider', 'Admin_tech'
	UNION
	SELECT 'affectation_pre_valider', 'Garde'
	UNION
	SELECT 'commission_valider_affectations', 'Admin_tech'
	UNION
	SELECT 'commission_valider_affectations', 'Garde'
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
CREATE TABLE IF NOT EXISTS affectation_stage_etat_linker (
	affectation_stage_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (affectation_stage_id, etat_instance_id),
	FOREIGN KEY (affectation_stage_id) REFERENCES affectation_stage (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);


INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('affectation','Affectation','fas fa-flag','#003264CC','12')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;

with categorie as (
	select * from unicaen_etat_categorie
	where code = 'affectation'
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

--  Avant la phase d'affectation
	select 'futur' as code, 'Future' as libelle, 'fas fa-clock'  as icone, 'muted'  as code_couleur, 1 as ordre
-- En cours = pendant la période d'affectation temps qu'elle est pas faite
	union select 'en_cours', 'Affectation en cours', 'far fa-hourglass-half', 'primary', 2
	union select 'en_retard', 'En attente d''affectation', 'far fa-hourglass-half', 'warning', 3
	union select 'proposition', 'Proposition d''affectation', 'far fa-circle-up', 'primary', 4
	union select 'pre_valide', 'Pré-validée', 'fas fa-check', 'primary', 5
	union select 'valide', 'Validée', 'fas fa-check-double', 'success', 6

	union select 'en_alerte', 'Affectation en alerte', 'fas fa-triangle-exclamation', 'warning', 7
	union select 'en_erreur', 'Affectation en erreur', 'fas fa-triangle-exclamation', 'danger', 8
	union select 'stage_non_effectue' , 'Stage non effectué' , 'fas fa-ban', 'muted' , 9
	union select 'en_disponibilite' , 'Étudiant en disponibilité' , 'fas fa-pause', 'muted' , 10
	union select 'non_affecte', 'Non affecté', 'far fa-times-circle', 'muted', 11

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
