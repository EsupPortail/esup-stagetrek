INSERT INTO unicaen_privilege_categorie (code, libelle, ordre, namespace)
VALUES ('fichier', 'Gesion de fichiers', 200, 'Fichier\Provider\Privilege');

INSERT INTO unicaen_privilege_privilege(CATEGORIE_ID, CODE, LIBELLE, ORDRE)
WITH d(code, lib, ordre) AS (
    SELECT 'fichier_afficher', 'Afficher l''index des fichiers', 1
    UNION SELECT 'fichier_televerser', 'Téléverser des fichiers', 2
    UNION SELECT 'fichier_telecharger', 'Télécharger des fichiers', 3
    UNION SELECT 'fichier_modifier', 'Modifier des fichiers', 4
    UNION SELECT 'fichier_archiver', 'Archiver des fichiers', 5
    UNION SELECT 'fichier_restaurer', 'Restaurer des fichiers', 6
    UNION SELECT 'fichier_supprimer', 'Supprimer des fichiers', 7
)
SELECT cp.id, d.code, d.lib, d.ordre
FROM d
JOIN unicaen_privilege_categorie cp ON cp.CODE = 'fichier'
;

-- Temporaire : pour des test :
with roles as (
	SELECT * from unicaen_utilisateur_role where role_id = 'Admin_tech'
),
privileges as (
	SELECT p.* from unicaen_privilege_privilege p
	    join unicaen_privilege_categorie c on p.categorie_id = c.id
	           where c.code = 'fichier'
)
INSERT INTO demo.public.unicaen_privilege_privilege_role_linker (role_id, privilege_id)
       SELECT roles.id, privileges.id FROM roles, privileges
on CONFLICT (role_id, privilege_id) do NOTHING;