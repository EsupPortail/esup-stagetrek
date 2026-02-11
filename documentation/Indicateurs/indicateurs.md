# UnicaenIndicateurs

Le module UnicaenIndicateurs permet de fournir des informations métriques (ou autres) aux utilisateurs via des requêtes SQL.



## Périmettre d'accés

UnicaenIndicateur permet de limité l'accés de certains indicateurs en fonction d'un périmétres associé à l'utilisateur
Consultze [Unicaen/indicateur](../../stagetrek/vendor/unicaen/indicateur/readme.md) pour plus d'informations sur la configuration à définir.


## Définition des indicateurs

La création d'indicateurs se fait via le menu `Administration > Indicateurs`

Un indicateurs se construit en définissant une vue matérilsé. Il est donc fortement recommmandé de n'autoriser celà qu'à un administrateur technique ayant une connaissance de la base de données.

Un cron définie dans [cron-service](../../deploy_configuration/core/cron/cron-service) mets à jours automatiquement ces vue matérialisées.

 Afin de ne pas "poluer" le fichier de ddl du module bdd_admin, il est conseillé de respecter la nomenclature 'mvi\_[XXXX]' comme identifiant de la vue associée._


### Indicateurs types

Voici une liste d'indicateurs possibles.

Cette liste est non exhaustives, a compléter au fur et a mesure des besoins. 
_Toutes contributions à cette liste est la bienvenue._

**mvi_etudiants_etats**

Donne le nombre d'étudiants dans un états
```sql
select coalesce(et.libelle, 'Indéterminé') as etat, count(e.id) as nb_etudiants from etudiant e
	                                                                                     left join etudiant_etat_linker el on el.etudiant_id = e.id
	                                                                                     left join unicaen_etat_instance ei on el.etat_instance_id = ei.id
	                                                                                     left join unicaen_etat_type et on ei.type_id = et.id
where (ei.id is null or ei.histo_destruction is null) --Uniquement les états en cours
GROUP BY et.libelle, et.ordre
ORDER BY et.ordre;
```

**vmi_etudiants_cursus_alertes**

Étudiants de DFASM3 n'ayant pas encore validé toutes les contraintes de cursus;


```sql
select
	CASE
		when p.code = 'categorie' then cat.libelle
		when p.code = 'terrain' then t.libelle
		when p.code = 'general' then c.libelle
		                          else 'Portée indeterminée, contrainte #' || c.id
	end as "Contrainte"
	 ,e.num_etu as "Numéro Etudiant",
	e.nom, e.prenom, e.email, ce.reste_a_satisfaire as "Nombre de stage restant à effectuer"
from etudiant e
	     join etudiant_groupe_linker gl on e.id = gl.etudiant_id
	     join groupe g on g.id = gl.groupe_id
	     join contrainte_cursus_etudiant ce on ce.etudiant_id = e.id
	     join contrainte_cursus c on c.id = ce.contrainte_id
	     join contrainte_cursus_portee p on p.id = c.portee
	     left join terrain_stage t on t.id = c.terrain_stage_id
	     left join categorie_stage cat on cat.id = c.categorie_stage_id
	     join annee_universitaire a on a.id = g.annee_universitaire_id
	     join niveau_etude n on n.id = g.niveau_etude_id
WHERE a.date_debut < now() and now() < a.date_fin  -- Uniquement l'année en cours
  and n.ordre = (select max(ordre) from niveau_etude) -- Dernier niveau d'étude
  and ce.active = true
  and ce.is_sat = false
  and reste_a_satisfaire > 0
  and validee_commission = false
ORDER BY cat.ordre, e.nom, e.prenom, e.num_etu
```

**mvi_stages_alertes**

Liste les étudiants ayant un stage en alerte ou en erreurs (par exemple l'affectation n'est pas validé)

```sql
SELECT a.libelle                                                                                                     AS annee
     , g.libelle                                                                                                     AS groupe
     , e.num_etu                                                                                                     AS "Numéro étudiant"
     , e.prenom                                                                                                      AS "Prénom"
     , e.nom                                                                                                         AS "Nom"
     , 'du ' || to_char( ss.date_debut_stage, 'DD/MM/YYYY' ) || ' au '
			|| to_char( ss.date_fin_stage, 'DD/MM/YYYY' )                                                                AS "Période"
     , s.numero_stage                                                                                                AS "stage numéro"
     , coalesce( et.libelle, 'Indéterminé' )                                                                         AS etat
     , ei.infos                                                                                                      AS "informations complementaires"
FROM stage s
	     JOIN stage_etat_linker el ON el.stage_id = s.id
	     LEFT JOIN unicaen_etat_instance ei ON el.etat_instance_id = ei.id
	     LEFT JOIN unicaen_etat_type et ON ei.type_id = et.id
	     JOIN etudiant e ON e.id = s.etudiant_id
	     JOIN session_stage ss ON ss.id = s.session_stage_id
	     JOIN groupe g ON ss.groupe_id = g.id
	     JOIN annee_universitaire a ON g.annee_universitaire_id = a.id
WHERE ( ei.id IS NULL OR ei.histo_destruction IS NULL ) --Uniquement les états en cours (ou sans états ce qui ne devrait pas existé)
  AND ( et.id IS NULL OR et.code IN ( 'stage_en_alerte', 'stage_en_erreur' ) )
ORDER BY et.ordre, ss.date_debut_stage DESC, num_etu
```


