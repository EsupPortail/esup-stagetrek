INSERT INTO "unicaen_utilisateur_user" (username, display_name, email, password)
VALUES
-- Utilisateur de l'application stagetrek n'en est pas vraiment 1 (aucun role/priviléges, permet surtout de gérer lorsque l'on doit enregistrer certaines actions faite automatiquement
( 'stagetrek', 'StageTrek', 'app', '')
-- 	 ,('USER1', 'Prenom Nom 1','Mail 1','shib')
-- 	 ,('USER2', 'Prenom Nom 2','Mail 2','shib')
ON CONFLICT (username) DO
	UPDATE set display_name = excluded.display_name,
	email = excluded.email,
	password = excluded.password;


-- Role par défaut des utilisateur en question
WITH data( usename, code_role ) AS (
	SELECT 'USER1', 'Admin_tech'
	UNION
	SELECT 'USER2', 'Admin_fonc'
)
   , old as ( -- On supprime les roles pour être sur de ne pas en avoir d'ancien
	DELETE from unicaen_utilisateur_role_linker
		where user_id in (
			select u.id from data d
				                 JOIN unicaen_utilisateur_user u ON u.username = d.usename
		)
)
INSERT
INTO unicaen_utilisateur_role_linker ( user_id, role_id )
SELECT u.id AS user_id, r.id AS role_id
FROM data d
	     JOIN unicaen_utilisateur_user u ON u.username = d.usename
	     JOIN unicaen_utilisateur_role r ON r.role_id = d.code_role
ON CONFLICT DO NOTHING;


select * from unicaen_utilisateur_user where username = 'stagetrek'