<?php

namespace BddAdmin\Migration;

use Application\Entity\Db\Source;
use Application\Provider\Roles\UserProvider;
use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20250917_Annee extends MigrationAction {

    public function description(): string
    {
        return "Insertion des sources et historique ppour les étudiants";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        if(!$this->manager()->hasTable('annee_universitaire')){
            return false;
        }

        return  $this->manager()->hasNewColumn('annee_universitaire', 'code');
    }

    public function before():void
    {

    }

    public function after():void
    {
        $bdd = $this->getBdd();
        $this->manager()->sauvegarderTable('annee_universitaire', 'save_annee');

        //Source code = num etu + champ non null
        $bdd->exec("update annee_universitaire set code = to_char(date_debut, 'YYYY') where code is null");
        $bdd->exec("alter table annee_universitaire alter column code set not null");
        $bdd->exec("alter table annee_universitaire add CONSTRAINT annee_code_unique UNIQUE (code)");

        $this->manager()->supprimerSauvegarde('save_annee');
    }

}