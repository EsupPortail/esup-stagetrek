-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS annee_universitaire (
	id bigserial PRIMARY KEY,
	libelle varchar(255) DEFAULT concat( EXTRACT( YEAR FROM now( ) ), '/', EXTRACT( YEAR FROM now( ) ) + 1 ),
	date_debut date NOT NULL DEFAULT date( concat( EXTRACT( YEAR FROM now( ) ), '-09-01' ) ),
	date_fin date NOT NULL DEFAULT date( concat( EXTRACT( YEAR FROM now( ) ) + 1, '-09-01' ) ),
	annee_verrouillee boolean DEFAULT FALSE,
	annee_universitaire_precedente_id bigint DEFAULT NULL,
	annee_universitaire_suivante_id bigint DEFAULT NULL,
	FOREIGN KEY ( annee_universitaire_precedente_id ) REFERENCES "annee_universitaire" ( id ) ON DELETE SET NULL,
	FOREIGN KEY ( annee_universitaire_suivante_id ) REFERENCES "annee_universitaire" ( id ) ON DELETE SET NULL
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 3, 'annee', 'Gestion des années universitaires', 'Application\Provider\Privilege'
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
WITH data(code, lib, ordre ) AS (
	SELECT 'annee_universitaire_afficher', 'Afficher les années universitaires', 1
	UNION
	SELECT 'annee_universitaire_ajouter', 'Ajouter une année universitaire', 2
	UNION
	SELECT 'annee_universitaire_modifier', 'Modifier les années universitaires', 3
	UNION
	SELECT 'annee_universitaire_supprimer', 'Supprimer des années universitaires', 4
	UNION
	SELECT 'annee_universitaire_valider', 'Valider une année universitaire', 5
	UNION
	SELECT 'annee_universitaire_deverrouiller', 'Déverrouiller une année universitaire', 6
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'annee'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'annee_universitaire_afficher', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_afficher', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_afficher', 'Scolarite'
	UNION
	SELECT 'annee_universitaire_afficher', 'Garde'
	UNION
	SELECT 'annee_universitaire_ajouter', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_ajouter', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_ajouter', 'Scolarite'
	UNION
	SELECT 'annee_universitaire_modifier', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_modifier', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_modifier', 'Scolarite'
	UNION
	SELECT 'annee_universitaire_supprimer', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_supprimer', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_valider', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_valider', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_valider', 'Scolarite'
	UNION
	SELECT 'annee_universitaire_deverrouiller', 'Admin_tech'
	UNION
	SELECT 'annee_universitaire_deverrouiller', 'Admin_fonc'
	UNION
	SELECT 'annee_universitaire_deverrouiller', 'Scolarite'
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
CREATE TABLE IF NOT EXISTS annee_universitaire_etat_linker (
	annee_universitaire_id BIGINT not null,
	etat_instance_id BIGINT not null,
	PRIMARY KEY (annee_universitaire_id, etat_instance_id),
	FOREIGN KEY (annee_universitaire_id) REFERENCES annee_universitaire (id) ON DELETE CASCADE,
	FOREIGN KEY (etat_instance_id) REFERENCES unicaen_etat_instance (id) ON DELETE CASCADE
);

INSERT INTO unicaen_etat_categorie (code, libelle, icone, couleur, ordre)
values ('annee','Année','fas fa-calendar','#003264CC','2')
on conflict (code) do update
	set libelle = excluded.libelle,
		icone = excluded.icone,
		couleur = excluded.couleur,
		ordre = excluded.ordre;


with categorie as (
	select * from unicaen_etat_categorie
	where code = 'annee'
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
	select 'en_construction'  as code, 'En construction' as libelle, 'fas fa-cogs' as icone, 'info' as code_couleur ,1 as ordre
	union select 'non_valide', 'Non validée ', 'fas fa-lock-open', 'danger', 2
	union select 'futur', 'Future', 'fas fa-clock', 'muted', 3
	union select 'en_cours', 'En cours', 'far fa-hourglass-half', 'primary', 4
	union select 'termine', 'Terminée', 'fas fa-check', 'success',5
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

