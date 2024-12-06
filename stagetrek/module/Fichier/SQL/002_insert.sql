--
INSERT INTO fichier_nature (code, libelle, description, ordre) VALUES
	('doc', 'Document', 'Document divers', 1)
ON CONFLICT (code) DO UPDATE
set libelle = excluded.libelle,
    description = excluded.description,
    ordre = excluded.ordre
;