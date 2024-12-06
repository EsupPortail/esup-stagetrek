-- Date de MAJ 27/03/2024 ----------------------------------------------------------------------------------------------

-- ------------------------------
-- -- TABLE ---------------------
-- ------------------------------
CREATE TABLE IF NOT EXISTS adresse_type (
	id bigserial PRIMARY KEY, code varchar(25) NOT NULL, libelle varchar(255) );
CREATE UNIQUE INDEX adresse_type_code_unique ON adresse_type ( code );

CREATE TABLE IF NOT EXISTS adresse (
	id bigserial PRIMARY KEY,
	adresse_type_id bigint NOT NULL DEFAULT 1,
	adresse varchar(255),
	complement varchar(255),
	ville varchar(255),
	code_postal varchar(5),
	cedex varchar(25),
	ville_code int DEFAULT 0,
	FOREIGN KEY ( adresse_type_id )
		REFERENCES adresse_type ( id )
		ON DELETE SET DEFAULT
);

-- ------------------------------
-- -- INSERT---------------------
-- ------------------------------
WITH data ( id, code, libelle ) AS (
	SELECT 1, 'n/a', 'Indetermine'
	UNION
	SELECT 2, 'etu', 'Etudiant'
	UNION
	SELECT 3, 'terrains', 'Terrain de stage'
)
INSERT INTO adresse_type ( id, code,  libelle)
       SELECT * from data
ON CONFLICT (code) DO UPDATE SET libelle=excluded.libelle;

-- ------------------------------
-- -- Priviléges ----------------
-- ------------------------------
-- Pas de priviléges car chaque adresse dépend de l'entité à laquelle elle est rataché
