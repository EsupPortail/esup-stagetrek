-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
-- Restiction d'accés de certains terrains à des niveaux d'étude ie. Médecine générale interdit en 3éme année
CREATE TABLE IF NOT EXISTS contrainte_terrain_stage_niveau_etude_linker (
	terrain_stage_id bigint NOT NULL,
	niveau_etude_id bigint NOT NULL,
	PRIMARY KEY ( terrain_stage_id, niveau_etude_id ),
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES "terrain_stage" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( niveau_etude_id )
		REFERENCES "niveau_etude" ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS contrainte_terrain_stage_niveau_etude_unique ON contrainte_terrain_stage_niveau_etude_linker ( terrain_stage_id, niveau_etude_id );

-- Pour les contraintes sur les entities
CREATE TABLE IF NOT EXISTS contrainte_cursus_portee (
	id bigserial PRIMARY KEY,
	code varchar(25) NOT NULL,
	libelle varchar(255) NOT NULL,
	ordre int DEFAULT 1 );
CREATE UNIQUE INDEX IF NOT EXISTS contrainte_cursus_portee_code_unique ON "contrainte_cursus_portee" ( code );

CREATE TABLE IF NOT EXISTS contrainte_cursus (
	id bigserial PRIMARY KEY,
	libelle varchar(255) NOT NULL,
	acronyme varchar(10) NOT NULL,
	portee bigint NOT NULL,
	description varchar(255) NOT NULL,
	categorie_stage_id bigint DEFAULT NULL,
	terrain_stage_id bigint DEFAULT NULL,
	nombre_de_stage_min int DEFAULT NULL,
	nombre_de_stage_max int DEFAULT NULL,
	date_debut date NOT NULL DEFAULT '2020-01-01',
	date_fin date NOT NULL DEFAULT '2030-12-31',
	ordre int DEFAULT 0,
	-- Est-ce que la contrainte en contredit une autre
	is_contradictoire boolean DEFAULT FALSE,
	FOREIGN KEY ( portee )
		REFERENCES contrainte_cursus_portee ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( categorie_stage_id )
		REFERENCES categorie_stage ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES terrain_stage ( id )
		ON DELETE CASCADE
);

-- Liens entre les étudiants et les contraintes
CREATE TABLE IF NOT EXISTS contrainte_cursus_etudiant (
	id bigserial PRIMARY KEY,
	etudiant_id bigint NOT NULL,
	contrainte_id bigint NOT NULL,
	active boolean DEFAULT TRUE,              -- False = l'étudiant n'a pas besoins de satisfaire cette contrainte
	validee_commission boolean DEFAULT FALSE, -- Si l'on souhaite manuellement dire que la contrainte de l'étudiant est satisfaite
	nb_equivalences int DEFAULT 0,            -- Nombre de stage accordée (considéré comme effectué) par équivalence
	reste_a_satisfaire int DEFAULT 0,            -- Nombre de stage restant à satisfaire pour la contrainte, sert nottament pour les recommandations de préférence
	is_sat boolean DEFAULT FALSE,               --est-ce que la contrainte est satisfaite
--  différentes valeurs calculer automatiquement pour déterminer la satisfaction todo : a supprimer
	nb_stages_validant int DEFAULT 0,
	can_be_sat boolean DEFAULT TRUE,
	is_in_contradiction boolean DEFAULT FALSE,
	-- clé étrangére
	FOREIGN KEY ( etudiant_id )
		REFERENCES etudiant ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( contrainte_id )
		REFERENCES contrainte_cursus ( id )
		ON DELETE CASCADE,
);
CREATE UNIQUE INDEX IF NOT EXISTS contrainte_cursus_etudiant_unique ON "contrainte_cursus_etudiant" ( etudiant_id, contrainte_id );


-- TODO : a revoir a l'usage avec contrainte_terrain_stage_niveau_etude_linker laquel on garde ou pas
-- a priori se serait mieux celle ci car permettrait de mettre des restrictions sur le catégories en plus de sur les entities
-- Refonte en cours
CREATE TABLE IF NOT EXISTS "contrainte_cursus_niveau_etude_linker"
(
	contrainte_cursus_id BIGINT NOT NULL,
	niveau_etude_id BIGINT NOT NULL,
	PRIMARY KEY (contrainte_cursus_id, niveau_etude_id),
	FOREIGN KEY (contrainte_cursus_id) REFERENCES "contrainte_cursus" (id) ON DELETE CASCADE,
	FOREIGN KEY (niveau_etude_id) REFERENCES "niveau_etude" (id) ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS contrainte_cursus_niveau_etude_unique ON contrainte_cursus_niveau_etude_linker (contrainte_cursus_id, niveau_etude_id);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
INSERT INTO contrainte_cursus_portee ( id, code, libelle, ordre )
VALUES ( 1, 'general', 'Général', 1 ), ( 2, 'categorie', 'Catégorie', 2 ), ( 3, 'terrain','Terrain de stage ', 3 )
ON CONFLICT (id) DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Catégorie de privilége - parametre
-- ----
WITH data( code_categorie, code, lib, ordre ) AS (
	SELECT 'parametre', 'parametre_contrainte_cursus_afficher', 'Afficher les contraintes de cursus des étudiants', 21
	UNION
	SELECT 'parametre', 'parametre_contrainte_cursus_ajouter', 'Ajouter une contrainte sur le cursus des étudiants', 22
	UNION
	SELECT  'parametre', 'parametre_contrainte_cursus_modifier', 'Modifier une contrainte de cursus des étudiants', 23
	UNION
	SELECT 'parametre', 'parametre_contrainte_cursus_supprimer', 'Supprimer une contrainte de cursus des étudiants', 24
-- 	Partie étudiants
	UNION
	SELECT 'etudiant', 'etudiant_contraintes_afficher', 'Afficher les contraintes du curusus d''un étudiant', 41
	UNION
	SELECT 'etudiant','etudiant_contrainte_modifier', 'Modifier une contrainte pour un étudiant', 42
	UNION
	SELECT 'etudiant','etudiant_contrainte_valider', 'Valider une contrainte pour un étudiant', 43
	UNION
	SELECT 'etudiant','etudiant_contrainte_invalider', 'Invalider une contrainte pour un étudiant', 44
	UNION
	SELECT 'etudiant','etudiant_contrainte_activer', 'Activer une contrainte pour un étudiant', 45
	UNION
	SELECT 'etudiant','etudiant_contrainte_desactiver', 'Désactiver une contrainte pour un étudiant', 46
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = d.code_categorie
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'parametre_contrainte_cursus_afficher', 'Admin_tech'
	UNION
	SELECT 'parametre_contrainte_cursus_afficher', 'Admin_fonc'
	UNION
	SELECT 'parametre_contrainte_cursus_ajouter', 'Admin_tech'
	UNION
	SELECT 'parametre_contrainte_cursus_ajouter', 'Admin_fonc'
	UNION
	SELECT 'parametre_contrainte_cursus_modifier', 'Admin_tech'
	UNION
	SELECT 'parametre_contrainte_cursus_modifier', 'Admin_fonc'
	UNION
	SELECT 'parametre_contrainte_cursus_supprimer', 'Admin_tech'
	UNION
	SELECT 'parametre_contrainte_cursus_supprimer', 'Admin_fonc'
--
	UNION
	SELECT 'etudiant_contraintes_afficher', 'Admin_tech'
	UNION
	SELECT 'etudiant_contraintes_afficher', 'Admin_fonc'
	UNION
	SELECT 'etudiant_contrainte_modifier', 'Admin_tech'
	UNION
	SELECT 'etudiant_contrainte_modifier', 'Admin_fonc'
	UNION
	SELECT 'etudiant_contrainte_valider', 'Admin_tech'
	UNION
	SELECT 'etudiant_contrainte_valider', 'Admin_fonc'
	UNION
	SELECT 'etudiant_contrainte_invalider', 'Admin_tech'
	UNION
	SELECT 'etudiant_contrainte_invalider', 'Admin_fonc'
	UNION
	SELECT 'etudiant_contrainte_activer', 'Admin_tech'
	UNION
	SELECT 'etudiant_contrainte_activer', 'Admin_fonc'
	UNION
	SELECT 'etudiant_contrainte_desactiver', 'Admin_tech'
	UNION
	SELECT 'etudiant_contrainte_desactiver', 'Admin_fonc'
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
CREATE TABLE IF NOT EXISTS contrainte_cursus_etudiant_etat_linker (
	contrainte_cursus_etudiant_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (contrainte_cursus_etudiant_id, etat_instance_id),
	FOREIGN KEY (contrainte_cursus_etudiant_id) REFERENCES contrainte_cursus_etudiant (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);

INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('contrainte_cursus','Contraintes de cursus','fas fa-check-square','#003264CC','12')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;


with categorie as (
	select * from unicaen_etat_categorie
	where code = 'contrainte_cursus'
)
   , color as (
	select 'primary' as code, '#0D6EFD' as code_hexa
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
-- Non satisfaite
-- Satisfaite
-- Validée par la commission
-- Désactivée
-- Alerte
-- Insatisfiable
-- Erreur
	select 'sat'  as code, 'Satisfaite' as libelle, 'fas fa-check' as icone, 'success' as code_couleur ,1 as ordre
	union select 'valide', 'Validée par la commission', 'fas fa-check-square', 'success',2
	union select 'non_sat', 'Non satisfaite', 'fas fa-hourglass-half', 'primary',3
	union select 'warning', 'À surveiller', 'fas fa-triangle-exclamation', 'warning',4
	union select 'insat', 'Insatifiable', 'fas fa-exclamation-circle', 'danger',5
	union select 'en_erreur', 'En erreur', 'fas fa-triangle-exclamation', 'danger', 6
	union select 'desactive', 'Désactivé', 'fas fa-times', 'muted', 7
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
