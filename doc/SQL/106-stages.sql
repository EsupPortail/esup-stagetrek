-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS stage (
	id bigserial PRIMARY KEY,
	numero_stage float NOT NULL DEFAULT 0,
	session_stage_id bigint NOT NULL,
	etudiant_id bigint NOT NULL,
	is_stage_secondaire boolean DEFAULT FALSE,
	stage_principal_id bigint DEFAULT NULL,
	stage_secondaire_id bigint DEFAULT NULL,
	stage_precedent_id int DEFAULT NULL,
	stage_suivant_id int DEFAULT NULL,
	score_pre_affectation float DEFAULT NULL,
	score_post_affectation float DEFAULT NULL,
	ordre_affectation int DEFAULT NULL,
	ordre_affectation_ow int DEFAULT NULL,                                          -- _ow = over write pour pouvoir le définir manuellement
	ordre_affectation_auto int DEFAULT NULL,                                        -- ordre d'affectation calculé automatiquement
	informations_complementaires text,
	stage_non_effectue boolean NOT NULL DEFAULT FALSE,
	FOREIGN KEY ( session_stage_id )
		REFERENCES "session_stage" ( id ),
	FOREIGN KEY ( stage_principal_id )
		REFERENCES stage ( id )
		ON DELETE CASCADE,                                                          -- supprimer le stage principal fait nécessairement la même pour le stage secondaire
	FOREIGN KEY ( stage_secondaire_id ) REFERENCES stage ( id ) ON DELETE SET NULL, -- mais pas l'inverse
	FOREIGN KEY ( stage_precedent_id ) REFERENCES "stage" ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( stage_suivant_id ) REFERENCES "stage" ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( etudiant_id )
		REFERENCES "etudiant" ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS stage_unique ON stage ( id, session_stage_id, etudiant_id );


-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 5, 'stage', 'Gestion des stages', 'Application\Provider\Privilege'
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
	SELECT 'stage_afficher', 'Afficher un stage', 1
	UNION
	SELECT 'stage_ajouter', 'Ajouter un stage', 2
	UNION
	SELECT 'stage_modifier', 'Modifier un stage', 3
	UNION
	SELECT 'stage_supprimer', 'Supprimer un stage', 4
	UNION
	SELECT 'etudiant_own_stages_afficher', 'Afficher ses sessions de stage', 5
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
	SELECT 'stage_afficher', 'Admin_tech'
	UNION
	SELECT 'stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'stage_afficher', 'Scolarite'
	UNION
	SELECT 'stage_afficher', 'Garde'
	UNION
	SELECT 'stage_ajouter', 'Admin_tech'
	UNION
	SELECT 'stage_ajouter', 'Admin_fonc'
	UNION
	SELECT 'stage_modifier', 'Admin_tech'
	UNION
	SELECT 'stage_modifier', 'Admin_fonc'
	UNION
	SELECT 'stage_supprimer', 'Admin_tech'
	UNION
	SELECT 'stage_supprimer', 'Admin_fonc'
	UNION
	SELECT 'etudiant_own_stages_afficher', 'Etudiant'
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
CREATE TABLE IF NOT EXISTS stage_etat_linker (
	stage_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (stage_id, etat_instance_id),
	FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);


INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('stage','Stage','fas fa-notes-medical','#003264CC','6')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;


with categorie as (
	select * from unicaen_etat_categorie
	where code = 'stage'
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
	select 'futur' as code, 'Future' as libelle, 'fas fa-clock'  as icone, 'muted'  as code_couleur, 1 as ordre
	union select 'preference', 'Phase de définition des préférences', 'fas fa-list-ol', 'primary', 2
	union select 'affectation', 'En cours d''attribution', 'fas fa-check-to-slot', 'primary', 3
	union select 'a_venir', 'Début du stage à venir', 'fas fa-clock', 'info', 4
	union select 'en_cours', 'Stage en cours', 'far fa-hourglass-half', 'primary', 5
	union select 'validation', 'En attente de validation', 'fas fa-check-square', 'info',6
	union select 'validation_retard', 'Validation non effectuée', 'fas fa-check-square', 'warning',7
	union select 'evaluation', 'En attente d''une évalutaion', 'fas fa-check-square', 'primary',8
	union select 'evaluation_retard', 'Évalutaion non effectuée', 'fas fa-check-square', 'warning',9
	union select 'termine_valide', 'Stage validé', 'fas fa-check', 'success',10
	union select 'termine_non_valide', 'Stage non validé', 'fas fa-times', 'danger',11
	union select 'en_alerte', 'Stage en alerte', 'fas fa-triangle-exclamation', 'warning', 12
	union select 'en_erreur', 'Stage en erreur', 'fas fa-triangle-exclamation', 'danger', 13
	union select 'non_effectue' , 'Stage non effectué' , 'fas fa-ban', 'muted' , 14
	union select 'en_disponibilite' , 'Étudiant en disponibilité' , 'fas fa-pause', 'muted' , 15
	union select 'desactive' , 'Stage désactivé' , 'fas fa-times', 'muted' , 16
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
