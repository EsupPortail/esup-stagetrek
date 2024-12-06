-- Table nécessaire au document
create table fichier_nature
(
	id  bigserial PRIMARY KEY,
	code        varchar(64)  not null,
	libelle     varchar(256) not null,
	description varchar(2048),
	ordre int DEFAULT 0 NOT NULL

);
create unique index fichier_nature_code_unique on fichier_nature (code);

/** !!! on n'utilise pas un id de type int ici mais de type varchar (permet entre autre de mieux gerer la génration d'un nom de fichier)*/
create table fichier_fichier
(
	id  varchar(25)  not null constraint fichier_fichier_pk primary key,
	nom_original          varchar(256) not null,
	nom_stockage          varchar(256) not null,
	nature                integer      not null,
	type_mime             varchar(256) not null,
	taille                varchar(256),
	histo_creation        timestamp    not null,
	histo_createur_id     integer      not null,
	histo_modification    timestamp,
	histo_modificateur_id integer,
	histo_destruction     timestamp,
	histo_destructeur_id  integer,

	FOREIGN KEY ( nature ) REFERENCES fichier_nature ( id ),
	FOREIGN KEY ( histo_createur_id ) REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_createur_id ) REFERENCES unicaen_utilisateur_user ( id ),
	FOREIGN KEY ( histo_createur_id ) REFERENCES unicaen_utilisateur_user ( id )
);
