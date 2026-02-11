-- vmi_etudiants_cursus_alertes
-- Listes des étudiants de 3émes années (pour l'année en cours) n'ayant pas validées toutes leurs contraintes de cursus
--
select
	CASE
		when p.code = 'categorie' then cat.libelle
		when p.code = 'terrain' then t.libelle
		when p.code = 'general' then c.libelle
		else 'Portée indeterminée, contrainte #' || c.id
		end as "Contrainte"
		,e.num_etu as "Numéro Etudiant",
		e.nom, e.prenom, e.email, ce.reste_a_satisfaire as "Nombre de stage restant àeffectuer"
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
;