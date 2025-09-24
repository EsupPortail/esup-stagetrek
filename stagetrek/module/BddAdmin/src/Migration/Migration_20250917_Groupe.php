<?php

namespace BddAdmin\Migration;

use Application\Entity\Db\Source;
use Application\Provider\Roles\UserProvider;
use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20250917_Groupe extends MigrationAction {

    public function description(): string
    {
        return "Ajout du champ code aux groupes";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        if(!$this->manager()->hasTable('groupe')){
            return false;
        }

        return  $this->manager()->hasNewColumn('groupe', 'code');
    }

    public function before():void
    {

    }

    public function after():void
    {

        $bdd = $this->getBdd();
        $this->manager()->sauvegarderTable('groupe', 'save_groupe');

        //Source code = num etu + champ non null
        $bdd->exec("update groupe set code = 'g_'||id  where code is null");
        $bdd->exec("alter table groupe alter column code set not null");
        $bdd->exec("alter table groupe add CONSTRAINT groupe_code_unique UNIQUE (code)");

        $this->manager()->supprimerSauvegarde('save_groupe');
    }

}