-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------
--  TODO : a remplacer prochainement par le module UNICAEN_PARAMETRES
-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS parametre_categorie (
	id bigserial PRIMARY KEY, code varchar(25) NOT NULL, libelle varchar(255) NOT NULL, ordre int DEFAULT 1
);
CREATE UNIQUE INDEX IF NOT EXISTS parametre_categorie_code_unique ON "parametre_categorie" ( code );



CREATE TABLE IF NOT EXISTS parametre_type (
	id bigserial PRIMARY KEY, code varchar(25) NOT NULL, libelle varchar(255) NOT NULL, cast_fonction varchar(10) DEFAULT NULL, ordre int DEFAULT 1
);
CREATE UNIQUE INDEX IF NOT EXISTS parametre_type_code_unique ON "parametre_type" ( code );


CREATE TABLE IF NOT EXISTS parametre (
	id bigserial PRIMARY KEY,
	categorie_id bigint NOT NULL,
	code varchar(64) NOT NULL,
	libelle varchar(255) NOT NULL,
	description varchar(255) NOT NULL,
	value varchar(255) NOT NULL,
	ordre int DEFAULT 1,
	parametre_type_id int DEFAULT NULL,
	FOREIGN KEY ( categorie_id )
		REFERENCES parametre_categorie ( id )
		ON DELETE CASCADE,
	FOREIGN KEY ( parametre_type_id ) REFERENCES parametre_type ( id ) ON DELETE SET NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS parametre_code_unique ON "parametre" ( code );


-- Cout des affectations des entities en fonctions du rang de satisfactions des préférences
CREATE TABLE IF NOT EXISTS "parametre_cout_affectation" (
	id bigserial PRIMARY KEY, rang int NOT NULL DEFAULT 1, cout int NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX IF NOT EXISTS parametre_cout_affectation_rang_unique ON "parametre_cout_affectation" ( rang );

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
-- TODo : a remplacer : l'id par un code
WITH data ( code, libelle, ordre ) AS (
	SELECT 'app', 'Applicatifs', 1
	UNION
	SELECT 'date', 'Dates des stages', 2
	UNION
	SELECT 'pref', 'Préférences', 3
	UNION
	SELECT 'validation_stage', 'Validation des stages', 4
	UNION
	SELECT 'procedure', 'Procédure d''affectation', 5
	UNION
	SELECT 'mail', 'Mails automatiques', 6
	UNION
	SELECT 'convention', 'Conventions de stages', 7
)
INSERT
INTO parametre_categorie ( code, libelle, ordre )
SELECT *
FROM data
ON CONFLICT (code) DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;


WITH data ( code, libelle, cast_fonction, ordre ) AS (
	SELECT 'n/a', 'Non Spécifié', NULL, 1
	UNION
	SELECT 'string', 'String', NULL, 2
	UNION
	SELECT 'int', 'Integer', 'intval', 3
	UNION
	SELECT 'float', 'Float', 'floatval', 4
	UNION
	SELECT 'bool', 'Boolean', 'boolval', 5
)
INSERT
INTO parametre_type ( code, libelle, cast_fonction, ordre )
SELECT *
FROM data
ON CONFLICT (code) DO UPDATE SET libelle=excluded.libelle
                             , cast_fonction=excluded.cast_fonction
                             , ordre=excluded.ordre;
--
-- !!! Certains paramètre spécifique Unicaen sont dans data-unicaen
WITH data ( code_categorie, code, libelle, description, value, ordre, code_parametre_type) AS (
	SELECT 'app'                                        AS code_categorie
		 , 'conservation_log'                           AS code
		 , 'Conservation des logs'                      AS libelle
		 , 'Nombre de jours de conservation des logs.' AS description
		 , '365'                                        AS value
		 , 1                                            AS ordre
		 , 'int'                                        AS code_parametre_type
	UNION
	SELECT 'app', 'conservation_mail', 'conservation des mails', 'Nombre de jours de conservation des mails.', '60', 2, 3
	UNION
	SELECT 'app'
		 , 'conservation_evenement'
		 , 'Conservation des événements'
		 , 'Nombre de jours de conservation des événements.'
		 , '60'
		 , 3
		 , 'int'
	UNION
	SELECT 'app'
		 , 'date_calcul_ordres_affectations'
		 , 'Date de calcul des ordres d''affectations'
		 , 'Le calcul automatique des ordres d''affectations est effectué a priori x jours avant le début du stage'
		 , '48'
		 , 1
		 , 'int'
	UNION
	SELECT 'date'
		 , 'date_phase_choix'
		 , 'Date de la phase de définition des préférences'
		 , 'La phase de choix commence a priori x jours avant le début du stage'
		 , '45'
		 , 2
		 , 'int'
	UNION
	SELECT 'date'
		 , 'duree_phase_choix'
		 , 'Durée de la phase de définition des préférences'
		 , 'Durée a priori de la phase de choix'
		 , '15'
		 , 3
		 , 'int'
	UNION
	SELECT 'date'
		 , 'date_phase_affectation'
		 , 'Date de la commission d''affectation'
		 , 'La commission d''affectation a lieu a priori x jours avant le début du stage'
		 , '15'
		 , 4
		 , 'int'
	UNION
	SELECT 'date', 'duree_stage', 'Durée d''un stage', 'Durée a priori d''un stage', '30', 5, 3
	UNION
	SELECT 'date'
		 , 'date_phase_validation'
		 , 'Date de la phase de validation'
		 , 'La phase de validation commence a priori x jours après le début du stage'
		 , '21'
		 , 6
		 , 'int'
	UNION
	SELECT 'date'
		 , 'duree_phase_validation'
		 , 'Durée de la phase de validation'
		 , 'Durée a priori de la phase de validation'
		 , '15'
		 , 7
		 , 'int'
	UNION
	SELECT 'date'
		 , 'date_phase_evaluation'
		 , 'Date de la phase d''évaluation'
		 , 'La phase d''évaluation commence a priori x jours après le début du stage'
		 , '31'
		 , 8
		 , 'int'
	UNION
	SELECT 'date'
		 , 'duree_phase_evaluation'
		 , 'Durée de la phase d''évaluation'
		 , 'Durée a priori de la phase d''évaluation'
		 , '15'
		 , 9
		 , 'int'
	UNION
	SELECT 'pref'
		 , 'nb_preferences'
		 , 'Nombre de preference(s)'
		 , 'Nombre de choix possible(s).'
		 , '12'
		 , 1
		 , 'int'
	UNION
	SELECT 'validation_stage'
		 , 'duree_token_validation'
		 , 'Durée de vie des tokens'
		 , 'Durée de vie des tokens de validations (en dehors de la phase de validation)'
		 , '15'
		 , 1
		 , 'int'
	UNION
	SELECT 'procedure'
		 , 'procedure_affectation'
		 , 'Procédure d''affectation'
		 , 'Code de la procédure d''affectation utillisée'
		 , 'v2'
		 , 1
		 , 'int'
	UNION
	SELECT 'procedure'
		 , 'coef_inadequation'
		 , 'Coefficient d''inadéquation'
		 , 'Coefficient d''inadéquation (en %) appliqué par l''algorithme de recommandation.'
		 , '10'
		 , 2
		 , 'int'
	UNION
	SELECT 'procedure', 'precision_cout_affectation', 'Précision des coûts', 'Precision du cout d''une affectation', '2', 2, 3
	UNION
	SELECT 'procedure'
		 , 'cout_terrain_max'
		 , 'Cout maximum d''un terrain de stage'
		 , 'Borne max pour le coût par défaut d''un terrain de stage'
		 , '40'
		 , 3
		 , 'float'
	UNION
	SELECT 'procedure'
		 , 'facteur_correcteur_cout_terrain'
		 , 'Facteur correcteur - Cout des terrains'
		 , 'Facteur correcteur appliqué lors du calcul du coûtd''un terrain de stage'
		 , '1.4'
		 , 4
		 , 'float'
	UNION
	SELECT 'procedure'
		 , 'facteur_correcteur_bonus_malus'
		 , 'Facteur correcteur - Bonus/Malus'
		 , 'Facteur correcteur appliqué lors du calcul du Bonus/Malus d''une affectation'
		 , '0.25'
		 , 5
		 , 'float'
	UNION
	SELECT 'procedure'
		 , 'cout_total_max'
		 , 'Cout maximum d''une affectation'
		 , 'Borne max pour le coût total d''une affectation de stage'
		 , '50'
		 , 6
		 , 'float'
	UNION
	SELECT 'mail'
		 , 'mail_no_response'
		 , 'Adresse d''envoie des mails'
		 , 'Adresse mail automatique d''envoie n''attendant pas de réponse'
		 , '[A DEFINIR]'
		 , 1
		 , 'string'
	UNION
	SELECT 'mail', 'mail_header', 'Entêtes des mails', 'Nom de l''application mis en entête des mails', 'Stagetrek', 2, 'string'
	UNION
	SELECT 'mail'
		 , 'date_planifictions_mails'
		 , 'Dates de planification des mails'
		 , 'Plannification des événements liés aux mails automatiques x jour avant leurs dates d''envoies.'
		 , '2'
		 , 3
		 , 'int'
	UNION
	SELECT 'mail'
		 , 'delai_rappels'
		 , 'Dates des mails de rappels'
		 , 'Envoie automatique d''un mail de rappel x jours avant la date de fin d''une phase si nécessaire.'
		 , '1'
		 , 4
		 , 'int'
	UNION
	SELECT 'convention'
		 , 'adresse_ufr_sante'
		 , 'Adresse UFR sante'
		 , 'Adresse de l''UFR de santé'
		 , '[A DEFINIR]'
		 , 1
		 , 'string'
	UNION
	SELECT 'convention', 'tel_ufr_sante', 'Tel UFR sante', 'Téléphonne de l''UFR de santé', '[A DEFINIR]', 2, 'string'
	UNION
	SELECT 'convention', 'fax_ufr_sante', 'Fax UFR sante', 'Fax de l''UFR de santé', '[A DEFINIR]', 3, 'string'
	UNION
	SELECT 'convention', 'doyen_ufr_sante', 'Doyen UFR sante', 'Nom du doyen de l''UFR de santé', '[A DEFINIR]', 4, 'string'
	UNION
	SELECT 'convention'
		 , 'directeur_chu'
		 , 'Directeur Général CHU'
		 , 'Directeur Général du Centre Hospitalier Universitaire'
		 , '[A DEFINIR]'
		 , 5
		 , 'string'
)
INSERT
INTO parametre ( categorie_id, code, libelle, description, value, ordre, parametre_type_id )
SELECT *
FROM (
	select cat.id, data.code, data.libelle, data.description, data.value, data.ordre, type.id from data
	         join parametre_categorie cat on data.code_categorie = cat.code
	         join parametre_type type on data.code_parametre_type = type.code
)
ON CONFLICT (code) DO UPDATE SET categorie_id = excluded.categorie_id
                               , libelle = excluded.libelle
                               , description = excluded.description
                               , value = excluded.value
                               , ordre = excluded.ordre
                               , parametre_type_id = excluded.parametre_type_id;

INSERT INTO parametre_cout_affectation ( id, rang, cout )
VALUES ( 1, 1, 5 )
	 , ( 2, 2, 4 )
	 , ( 3, 3, 3 )
	 , ( 4, 4, 3 )
	 , ( 5, 5, 2 )
	 , ( 6, 6, 2 )
	 , ( 7, 7, 2 )
	 , ( 8, 8, 1 )
	 , ( 9, 9, 1 )
	 , ( 10, 10, 1 )
	 , ( 11, 11, 1 )
	 , ( 12, 12, 1 )
ON CONFLICT (rang) DO UPDATE SET cout = excluded.cout;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 201, 'parametre', 'Gestion des paramètres', 'Application\Provider\Privilege'
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
	SELECT 'parametre_afficher', 'Afficher les paramètres de l''application', 11
	UNION
	SELECT 'parametre_ajouter', 'Ajouter un paramètre de l''application', 12
	UNION
	SELECT 'parametre_modifier', 'Modifier un paramètre de l''application', 13
	UNION
	SELECT 'parametre_supprimer', 'Supprimer un paramètre de l''application', 14
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'parametre'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'parametre_afficher', 'Admin_tech'
	UNION
	SELECT 'parametre_afficher', 'Admin_fonc'
	UNION
	SELECT 'parametre_ajouter', 'Admin_tech'
	UNION
	SELECT 'parametre_modifier', 'Admin_tech'
	UNION
	SELECT 'parametre_modifier', 'Admin_fonc'
	UNION
	SELECT 'parametre_supprimer', 'Admin_tech'
	UNION
	SELECT 'parametre_supprimer', 'Admin_tech'
)
   , role_linker AS (
	SELECT role.id AS role_id, privilege.id AS privilege_id, d.*
	FROM data d
		     JOIN unicaen_utilisateur_role role ON d.code_role = role.role_id
		     JOIN unicaen_privilege_privilege privilege ON d.code_privilege = privilege.code
)
   , old AS ( -- Suppression des anciennes matrice
	DELETE FROM unicaen_privilege_privilege_role_linker l WHERE l.privilege_id
			IN (
			SELECT DISTINCT privilege_id
			FROM role_linker
		)
)
INSERT
INTO unicaen_privilege_privilege_role_linker ( role_id, privilege_id )
	(
		SELECT role_id, privilege_id
		FROM role_linker
	)
ON CONFLICT (role_id, privilege_id) DO UPDATE -- Pour concerver ceux "supprimé mais qui doivent rester"
	SET role_id = excluded.role_id, privilege_id=excluded.privilege_id;