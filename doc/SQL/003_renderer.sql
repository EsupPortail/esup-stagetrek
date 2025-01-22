-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE unicaen_renderer_macro (
	id serial NOT NULL
		CONSTRAINT unicaen_document_macro_pk PRIMARY KEY,
	code varchar(256) NOT NULL,
	description text,
	variable_name varchar(256) NOT NULL,
	methode_name varchar(256) NOT NULL
);
CREATE UNIQUE INDEX unicaen_document_macro_code_uindex ON unicaen_renderer_macro ( code );
CREATE UNIQUE INDEX unicaen_document_macro_id_uindex ON unicaen_renderer_macro ( id );

CREATE TABLE unicaen_renderer_template (
	id serial NOT NULL
		CONSTRAINT unicaen_document_template_pk PRIMARY KEY,
	code varchar(256) NOT NULL,
	description text,
	namespace varchar(1024),
	engine varchar(64) default 'default' not null,
	document_type varchar(256) NOT NULL,
	document_sujet text NOT NULL,
	document_corps text NOT NULL,
	document_css text
);
CREATE UNIQUE INDEX unicaen_document_template_code_uindex ON unicaen_renderer_template ( code );

CREATE TABLE unicaen_renderer_rendu (
	id serial NOT NULL
		CONSTRAINT unicaen_document_rendu_pk PRIMARY KEY, template_id int DEFAULT NULL
		CONSTRAINT unicaen_document_rendu_template_id_fk
			REFERENCES unicaen_renderer_template
			ON DELETE SET NULL, date_generation timestamp NOT NULL, sujet text NOT NULL, corps text NOT NULL
);

-- ------------------------------
-- -- Insertion -----------------
-- ------------------------------
--  Liste des macros, a maintenir a jours. Choix fait de les ordonnées alphabétiquement
-- Convention de nomage des codes : Object#attribut
WITH data ( code, description, variable_name, methode_name ) AS (
	SELECT 'AnneeUniversitaire#libelle', '<p>Libelle de l''année universitaire', 'anneeUniversitaire', 'getLibelle'
	UNION
	SELECT 'Application#url', '<p>Url de l''application</p>', 'urlService', 'getUrlApp'
	UNION
	SELECT 'CHU#nomCHU', '<p>Nom du CHU</p>', 'parametreService', 'getNomCHU'
	--
	UNION
	SELECT 'CHU#directeurGeneral'
		 , '<p>Nom Prénom du directeur général du CHU</p>'
		 , 'parametreService'
		 , 'getDirecteurCHU'
	UNION
	SELECT 'CHU#doyenUFRSante', '<p>Nom Prénom du doyen de l''UFR de santé</p>', 'parametreService', 'getDoyenUFR'
	--
	UNION
	SELECT 'Etudiant#adresse'
		 , '<p>Adresse personnel de l''étudiant.e</p>'
		 , 'adresseRendererService'
		 , 'getAdresseEtudiant'
	UNION
	SELECT 'Etudiant#dateNaissance'
		 , '<p>Date de naissance de l''étudiant.e</p>'
		 , 'dateRendererService'
		 , 'getDateNaissanceEtudiant'
	UNION
	SELECT 'Etudiant#mail', '<p>Adresse mail de l''étudiant.e</p>', 'etudiant', 'getEmail'
	UNION
	SELECT 'Etudiant#nom', '<p>Nom de l''étudiant.e</p>', 'etudiant', 'getNom'
	UNION
	SELECT 'Etudiant#nomPrenom', '<p>Nom Prénom de l''étudiant.e</p>', 'etudiant', 'getDisplayName'
	UNION
	SELECT 'Etudiant#numero', '<p>Numéro l''étudiant.e</p>', 'etudiant', 'getNumEtu'
	UNION
	SELECT 'Etudiant#prenom', '<p>Prénom de l''étudiant.e</p>', 'etudiant', 'getPrenom'
	--
	UNION
	SELECT 'UFR#nom', '<p>Nom de l''UFR de santé</p>', 'parametreService', 'getNomUFRSante'
	UNION
	SELECT 'UFR#adresse', '<p>Adresse de l''UFR de santé</p>', 'parametreService', 'getAdresseUFR'
	UNION
	SELECT 'UFR#mail', '<p>Adresse mail de contact à l''UFR de santé</p>', 'parametreService', 'getMailUFR'
	UNION
	SELECT 'UFR#fax', '<p>Fax à l''UFR de santé</p>', 'parametreService', 'getFaxUFR'
	UNION
	SELECT 'UFR#Tel', '<p>Téléphone de contact à l''UFR de santé</p>', 'parametreService', 'getTelUFR'
	--
	UNION
	SELECT 'PDF#numeroPage'
		 , '<p>Numéro de la page courante dans le pdf généré</p>'
		 , 'pdfRendererService'
		 , 'getNumeroPage'
	UNION
	SELECT 'PDF#nombreDePages', '<p>Nombre total de page du pdf généré</p>', 'pdfRendererService', 'getNbPages'
	--
	UNION
	SELECT 'Preferences#nbChoix'
		 , '<p>Nombre de choix possible(s) pour définir les préférences</p>'
		 , 'parametreService'
		 , 'getNbChoixPossible'
	UNION
	SELECT 'Preferences#url'
		 , '<p>Url vers la page de définition des préférences de l''étudiant vers sont stage</p>'
		 , 'urlService'
		 , 'getUrlPreferences'
	--
	UNION
	SELECT 'Session#dateDebutStage'
		 , '<p>Date de début d''une session de stage</p>'
		 , 'dateRendererService'
		 , 'getDateDebutSessionStage'
	UNION
	SELECT 'Session#dateFinStage'
		 , '<p>Date de fin d''une session de stage</p>'
		 , 'dateRendererService'
		 , 'getDateFinSessionStage'
	--
	UNION
	SELECT 'Stage#dateDebut', '<p>Date de début du stage</p>', 'dateRendererService', 'getDateDebutStage'
	UNION
	SELECT 'Stage#dateDebutChoix'
		 , '<p>Date de début de la phase de choix du stage</p>'
		 , 'dateRendererService'
		 , 'getDateDebutChoixStage'
	UNION
	SELECT 'Stage#dateFin', '<p>Date de fin du stage</p>', 'dateRendererService', 'getDateFinStage'
	UNION
	SELECT 'Stage#dateFinChoix'
		 , '<p>Date de fin de la phase de choix du stage</p>'
		 , 'dateRendererService'
		 , 'getDateFinChoixStage'
	UNION
	SELECT 'Stage#dateFinValidation'
		 , '<p>Date de fin de la phase de validation du stage</p>'
		 , 'dateRendererService'
		 , 'getDateFinValidationStage'
	UNION
	SELECT 'Stage#heureDebutChoix'
		 , '<p>Heure de début de la phase de choix du stage</p>'
		 , 'dateRendererService'
		 , 'getHeureDebutChoixStage'
	UNION
	SELECT 'Stage#heureFinChoix'
		 , '<p>Heure de fin de la phase de choix du stage</p>'
		 , 'dateRendererService'
		 , 'getHeureFinChoixStage'
	UNION
	SELECT 'Stage#libelle', '<p>Libelle du stage</p>', 'stage', 'getLibelle'
	UNION
	SELECT 'Stage#mailResponsable'
		 , '<p>Adresse mail du responsable de stage</p>'
		 , 'contactRendererService'
		 , 'getMailResponsables'
	UNION
	SELECT 'Stage#responsable'
		 , '<p>Nom Prénom du responsable de stage</p>'
		 , 'contactRendererService'
		 , 'getResponsablesNames'
	UNION
	SELECT 'Stage#urlValidation', '<p>Url de validation du stage</p>', 'urlService', 'getUrlValidationStage'
	--
	UNION
	SELECT 'Terrain#libelle', '<p>Libelle du terrain de stage</p>', 'terrainStage', 'getLibelle'
	UNION
	SELECT 'Terrain#service', '<p>Nom du service du stage</p>', 'terrainStage', 'getService'
)
INSERT
INTO unicaen_renderer_macro ( code, description, variable_name, methode_name )
SELECT *
FROM data
ON CONFLICT(code)
	DO UPDATE SET description = excluded.description
	            , variable_name = excluded.variable_name
	            , methode_name = excluded.methode_name;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 207, 'documentmacro', 'UnicaenRenderer - Gestion des macros', 'UnicaenRenderer\Provider\Privilege'
	UNION
	SELECT 208, 'documenttemplate', 'UnicaenRenderer - Gestion des templates', 'UnicaenRenderer\Provider\Privilege'
	UNION
	SELECT 209, 'documentcontenu', 'UnicaenRenderer - Gestion des contenus', 'UnicaenRenderer\Provider\Privilege'
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
WITH data( code_categorie, code, lib, ordre ) AS (
	SELECT 'documentmacro', 'documentmacro_index', 'Afficher l''index des macros', 1
	UNION
	SELECT 'documentmacro', 'documentmacro_ajouter', 'Ajouter une macro', 2
	UNION
	SELECT 'documentmacro', 'documentmacro_modifier', 'Modifier une macro', 3
	UNION
	SELECT 'documentmacro', 'documentmacro_supprimer', 'Supprimer une macro', 4
--
	UNION
	SELECT 'documenttemplate', 'documenttemplate_index', 'Afficher l''index des contenus', 1
	UNION
	SELECT 'documenttemplate', 'documenttemplate_afficher', 'Afficher un template', 2
	UNION
	SELECT 'documenttemplate', 'documenttemplate_ajouter', 'Ajouter un contenu', 3
	UNION
	SELECT 'documenttemplate', 'documenttemplate_modifier', 'Modifier un contenu', 4
	UNION
	SELECT 'documenttemplate', 'documenttemplate_supprimer', 'Supprimer un contenu', 5
--
	UNION
	SELECT 'documentcontenu', 'documentcontenu_index', 'Accès à l''index des contenus', 1
	UNION
	SELECT 'documentcontenu', 'documentcontenu_afficher', 'Afficher un contenu', 2
	UNION
	SELECT 'documentcontenu', 'documentcontenu_supprimer', 'Supprimer un contenu ', 3
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
	SELECT 'documentmacro_index', 'Admin_tech'
	UNION
	SELECT 'documentmacro_ajouter', 'Admin_tech'
	UNION
	SELECT 'documentmacro_modifier', 'Admin_tech'
	UNION
	SELECT 'documentmacro_supprimer', 'Admin_tech'
	UNION
	SELECT 'documenttemplate_index', 'Admin_tech'
	UNION
	SELECT 'documenttemplate_index', 'Admin_fonc'
	UNION
	SELECT 'documenttemplate_afficher', 'Admin_tech'
	UNION
	SELECT 'documenttemplate_afficher', 'Admin_fonc'
	UNION
	SELECT 'documenttemplate_ajouter', 'Admin_tech'
	UNION
	SELECT 'documenttemplate_modifier', 'Admin_fonc'
	UNION
	SELECT 'documenttemplate_modifier', 'Admin_fonc'
	UNION
	SELECT 'documenttemplate_supprimer', 'Admin_tech'
	UNION
	SELECT 'documenttemplate_supprimer', 'Admin_fonc'
	UNION
	SELECT 'documentcontenu_index', 'Admin_tech'
	UNION
	SELECT 'documentcontenu_afficher', 'Admin_tech'
	UNION
	SELECT 'documentcontenu_supprimer', 'Admin_tech'
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