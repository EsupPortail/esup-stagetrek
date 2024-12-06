-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS unicaen_mail_mail (
	id serial NOT NULL
		CONSTRAINT umail_pkey PRIMARY KEY,
	date_envoi timestamp NOT NULL,
	status_envoi varchar(256) NOT NULL,
	destinataires text NOT NULL,
	destinataires_initials text,
	copies text,
	sujet text,
	corps text,
	mots_clefs text,
	log text
);

CREATE UNIQUE INDEX IF NOT EXISTS ummail_id_uindex ON unicaen_mail_mail ( id );

-- ------------------------------
-- -- Insertion -----------------
-- ------------------------------
WITH data ( code, description, document_sujet, document_corps, document_css ) AS (
	SELECT 'AffectationStage-Validee'   as code
		 , 'Mail envoyé aux étudiants pour les informer d''une modification de leurs affectations.' as description
		 , 'VAR[Stage#libelle] - De nouvelles informations sont disponibles' as document_sujet
		 , '<p>Cher(e), VAR[Etudiant#prenom]<br /><br /> Ce courriel vous est adressé car vous êtes inscrit pour le stage clinique VAR[Stage#libelle] qui aura lieux du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br /> De nouvelles informations concernant votre affectation de stage sont disponibles.<br />Vous pouvez dès à présent les consulter en vous connectant sur la plateforme VAR[Application#url].<br /> <br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>' as document_corps
		 , '' as document_css
	UNION
	SELECT 'StageDebutChoix'
		 , 'Mail envoyé aux étudiants pour les prévenir du début de la phase de définition des préférences d''une session de stage'
		 , 'VAR[Stage#libelle] - Début de la procédure de choix'
		 , '<br />Cher(e), VAR[Etudiant#prenom]<br /><br />Ce courriel vous est adressé car vous êtes inscrit pour le stage clinique VAR[Stage#libelle] qui aura lieux du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br />La procédure de choix est ouverte jusqu''au <strong>VAR[Stage#dateFinChoix] - VAR[Stage#heureFinChoix]</strong>.<br />Vous pouvez dès à présent définir vos préférences pour ce stage en vous connectant sur la plateforme VAR[Application#url].<br /><br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM'
		 , ''
	UNION
	SELECT 'StageDebutChoix-Rappel'
		 , 'Mail de rappel envoyé avant la date de fin de la procédure aux étudiants qui n''ont pas définie de préférence pour une session de stage'
		 , 'Rappel - VAR[Stage#libelle] - Procédure de choix'
		 , '<p><br />Cher(e), VAR[Etudiant#prenom]<br /><br />La procédure de choix pour stage clinique VAR[Stage#libelle] arrive a son terme.<br /><br />Vous pouvez définir ou modifier vos préférences jusqu''au VAR[Stage#dateFinChoix] - VAR[Stage#heureFinChoix] en vous connectant sur la plateforme VAR[Application#url].<br /><br /><strong>En l''absence de préférences la commission stage et garde procédera à votre affectation en fonction des places disponibles restantes. Aucune réclamation ne sera acceptée.</strong><br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>'
		 , ''
	UNION
	SELECT 'ValidationStage'
		 , 'Envoie du token de validation au responsable d''un stage'
		 , 'Validation du stage de VAR[Etudiant#nomPrenom]'
		 , '<p>Bonjour,<br /><br />Vous recevez ce mail en tant qu''encadrant pédagogique du stage de VAR[Etudiant#nomPrenom] sur la période du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br />Merci de procéder à l''évaluation du stage en allant sur le lien suivant VAR[Stage#urlValidation] avant le VAR[Stage#dateFinValidation].<br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM<br /><br /> <em>Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</em></p>'
		 , ''
	UNION
	SELECT 'ValidationStage-Effectuee'
		 , 'Mail envoyé automatiquement la première fois que le responsable pédagogique a procédé à la validation (ou l''invalidation) du stage'
		 , 'VAR[Stage#libelle] - Évaluation du stage effectuée'
		 , 'Cher(e), VAR[Etudiant#prenom]<br /><br />Votre encadrant pédagogique s''est prononcé sur la validation de votre stage VAR[Stage#libelle].<br />Cette évaluation sera disponible sur la plateforme VAR[Application#url] à partir du VAR[Stage#dateFinValidation].<br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM'
		 , ''
	UNION
	SELECT 'ValidationStage-Rappel'
		 , 'Ré-envoie du token de validation au responsable d''un stage si celui ci n''as pas effectuer la validation'
		 , 'Rappel - Validation du stage de VAR[Etudiant#nomPrenom]'
		 , '<p>Bonjour,<br /><br />Vous recevez ce mail en tant qu''encadrant pédagogique du stage de VAR[Etudiant#nomPrenom] sur la période du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br />A ce jour, nous n''avons pas reçu de validation de son stage.<br />La validation est rapide et s''effectue en allant sur le lien suivant VAR[Stage#urlValidation].<br /><br />Nous vous remercions par avance.<br /><br /> Bien cordialement,<br />La commission Stages et Gardes du DFASM<br /><br />Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</p>'
		 , ''
)
INSERT
INTO unicaen_renderer_template ( code, description, document_type, document_sujet, document_corps, document_css, namespace )
SELECT d.code, d.description, 'mail', d.document_sujet, d.document_corps, d.document_css, 'Mail'
FROM data d
ON CONFLICT(code)
	DO UPDATE SET description = excluded.description
	            , document_type = excluded.document_type
	            , document_sujet = excluded.document_sujet
	            , document_corps = excluded.document_corps
	            , document_css = excluded.document_css
	            , namespace = excluded.namespace;


-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
WITH categories( ordre, code, libelle, namespace ) AS (
	SELECT 210, 'mail', 'UnicaenMail - Gestion des mails', 'UnicaenMail\Provider\Privilege'
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
	SELECT 'mail_index', 'Afficher les mails envoyés', 1
	UNION
	SELECT 'mail_afficher', 'Afficher un mail spécifique', 2
	UNION
	SELECT'mail_reenvoi', 'Ré-envoi de mail', 3
	UNION
	SELECT 'mail_supprimer', 'Suppression d''un mail', 4
	UNION
	SELECT'mail_test', 'Envoi d''un mail de test', 5
)
INSERT
INTO unicaen_privilege_privilege( categorie_id, code, libelle, ordre )
SELECT cp.id, d.code, d.lib, d.ordre
FROM data d
	     JOIN unicaen_privilege_categorie cp ON cp.code = 'mail'
ON CONFLICT (categorie_id, code)
	DO UPDATE SET libelle=excluded.libelle, ordre=excluded.ordre;
-- ----
WITH data ( code_privilege, code_role ) AS (
	SELECT 'mail_index', 'Admin_tech'
	UNION
	SELECT 'mail_index', 'Admin_fonc'
	UNION
	SELECT 'mail_afficher', 'Admin_tech'
	UNION
	SELECT 'mail_afficher', 'Admin_fonc'
	UNION
	SELECT 'mail_reenvoi', 'Admin_tech'
	UNION
	SELECT 'mail_supprimer', 'Admin_tech'
	UNION
	SELECT 'mail_test', 'Admin_tech'
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