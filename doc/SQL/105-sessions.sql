-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------

CREATE TABLE IF NOT EXISTS session_stage (
	id bigserial PRIMARY KEY,
	libelle varchar(255),
	annee_universitaire_id bigint,
	groupe_id bigint NOT NULL,
	date_calcul_ordres_affectations timestamp NOT NULL,
	date_debut_choix timestamp NOT NULL,
	date_fin_choix timestamp NOT NULL,
	date_commission timestamp NOT NULL,
	date_debut_stage timestamp NOT NULL,
	date_fin_stage timestamp NOT NULL,
	date_debut_validation timestamp NOT NULL,
	date_fin_validation timestamp NOT NULL,
	date_debut_evaluation timestamp NOT NULL,
	date_fin_evaluation timestamp NOT NULL,
	session_rattrapage boolean NOT NULL DEFAULT FALSE,
	session_stage_precedente_id bigint DEFAULT NULL,
	session_stage_suivante_id bigint DEFAULT NULL,
	FOREIGN KEY ( annee_universitaire_id )
		REFERENCES "annee_universitaire" ( id ),
	FOREIGN KEY ( groupe_id )
		REFERENCES "groupe" ( id ),
	FOREIGN KEY ( session_stage_precedente_id ) REFERENCES "session_stage" ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( session_stage_suivante_id ) REFERENCES "session_stage" ( id ) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS session_stage_etudiant_linker (
	session_stage_id bigint NOT NULL,
	etudiant_id bigint NOT NULL,
	PRIMARY KEY ( session_stage_id, etudiant_id ),
	FOREIGN KEY ( session_stage_id )
		REFERENCES "session_stage" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( etudiant_id )
		REFERENCES "etudiant" ( id )
		ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS session_stage_etudiant_linker_unique ON session_stage_etudiant_linker ( session_stage_id, etudiant_id );



CREATE TABLE IF NOT EXISTS terrain_stage_niveau_demande(
	id bigserial PRIMARY KEY,
	code varchar(25) NOT NULL,
	libelle varchar(100) NOT NULL,
	ordre int NOT NULL DEFAULT 1,
	CONSTRAINT terrain_stage_niveau_demande_code_unique UNIQUE ( code )
);


-- Lien entre les session et les entities
CREATE TABLE IF NOT EXISTS "session_stage_terrain_linker" (
	id bigserial PRIMARY KEY,
	session_stage_id bigint NOT NULL,
	terrain_stage_id bigint NOT NULL,
	nb_places_ouvertes int DEFAULT 0 NOT NULL,
	nb_places_recommandees int DEFAULT 0 NOT NULL,
	nb_places_pre_affectees int DEFAULT 0 NOT NULL, -- = nombre de stage affecté sur le terrain qu'elles soient validée, pré-validée, en proposition ...
	nb_places_affectees int DEFAULT 0 NOT NULL,     -- = nombre de stage dont l'affectation sur le terrain est confirmé
	nb_places_disponibles int DEFAULT 0 NOT NULL,   -- = nombre de places non ouvertes sur la période du stage (par rapport au max)
	terrain_stage_niveau_demande_id bigint DEFAULT null, -- niveau de la demande sur le terrain de stage
	FOREIGN KEY ( session_stage_id )
		REFERENCES "session_stage" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_id )
		REFERENCES "terrain_stage" ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( terrain_stage_niveau_demande_id )
		REFERENCES terrain_stage_niveau_demande ( id )
		ON DELETE SET NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS session_terrain_linker_unique ON session_stage_terrain_linker ( session_stage_id, terrain_stage_id );


-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

INSERT into terrain_stage_niveau_demande(ordre, code, libelle)
VALUES ( 1, 'rang_1', '1er décile' )
	 , ( 2, 'rang_2', '2nd décile' )
	 , ( 3, 'rang_3', '3ème décile' )
	 , ( 4, 'rang_4', '4ème décile' )
	 , ( 5, 'rang_5', '5éme décile' )
	 , ( 6, 'no_demande', 'Aucune demande' )
	 , ( 7, 'ferme', 'Terrain fermé' )
	 , ( 8, 'n/a', 'Indéterminé' )
ON CONFLICT (code) DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 4, 'session', 'Gestion des sessions de stages', 'Application\Provider\Privilege'
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
-- ----
WITH data( code, lib, ordre ) AS (
	SELECT 'session_stage_afficher', 'Afficher les sessions de stages', 1
	UNION
	SELECT 'session_stage_ajouter', 'Ajouter des sessions de stages', 2
	UNION
	SELECT 'session_stage_modifier', 'Modifier les sessions de stages', 3
	UNION
	SELECT 'session_stage_supprimer', 'Supprimer des sessions de stages', 4
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'session'
	ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'session_stage_afficher', 'Admin_tech'
	UNION
	SELECT 'session_stage_afficher', 'Admin_fonc'
	UNION
	SELECT 'session_stage_afficher', 'Scolarite'
	UNION
	SELECT 'session_stage_afficher', 'Garde'
	UNION
	SELECT 'session_stage_ajouter', 'Admin_tech'
	UNION
	SELECT 'session_stage_ajouter', 'Admin_fonc'
	UNION
	SELECT 'session_stage_ajouter', 'Scolarite'
	UNION
	SELECT 'session_stage_modifier', 'Admin_tech'
	UNION
	SELECT 'session_stage_modifier', 'Admin_fonc'
	UNION
	SELECT 'session_stage_modifier', 'Scolarite'
	UNION
	SELECT 'session_stage_supprimer', 'Admin_tech'
	UNION
	SELECT 'session_stage_supprimer', 'Admin_fonc'
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
CREATE TABLE IF NOT EXISTS session_stage_etat_linker (
	session_stage_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (session_stage_id, etat_instance_id),
	FOREIGN KEY (session_stage_id) REFERENCES session_stage (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);


INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('session','Session de stage','fas fa-briefcase-medical','#003264CC','5')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;


with categorie as (
	select * from unicaen_etat_categorie
	where code = 'session'
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
	union select 'affectation', 'Phase d''affectation', 'fas fa-check-to-slot', 'primary', 3
	union select 'a_venir', 'Début des stages à venir', 'fas fa-clock', 'info', 4
	union select 'en_cours', 'Stages en cours', 'far fa-hourglass-half', 'primary', 5
	union select 'validation', 'Phase de validation', 'fas fa-check-square', 'info',6
	union select 'evaluation', 'Phase d''évalutaion', 'fas fa-check-square', 'primary',7
	union select 'termine', 'Session terminée', 'fas fa-check', 'success',8
	union select 'en_alerte', 'Session en alerte', 'fas fa-triangle-exclamation', 'warning', 9
	union select 'en_erreur', 'Session en erreur', 'fas fa-triangle-exclamation', 'danger', 10
	union select 'desactive' , 'Session désactivé' , 'fas fa-times', 'muted' , 11
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