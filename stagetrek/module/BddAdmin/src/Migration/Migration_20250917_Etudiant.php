<?php

namespace BddAdmin\Migration;

use Application\Entity\Db\Source;
use Application\Misc\Util;
use Application\Provider\Roles\UserProvider;
use Unicaen\BddAdmin\Migration\MigrationAction;
use UnicaenUtilisateur\Provider\Role\Username;

class Migration_20250917_Etudiant extends MigrationAction {

    public function description(): string
    {
        return "Insertion des sources et historique pour les étudiant".Util::POINT_MEDIANT."s";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        if(!$this->manager()->hasTable('etudiant')){
            return false;
        }

        return  $this->manager()->hasNewColumn('etudiant', 'source_code')
            ||  $this->manager()->hasNewColumn('etudiant', 'source_id')
            ||  $this->manager()->hasNewColumn('etudiant', 'histo_createur_id')
            ||  $this->manager()->hasNewColumn('etudiant', 'histo_creation');
    }

    public function before():void
    {

    }

    public function after():void
    {
        $bdd = $this->getBdd();

        //Source code = num etu + champ non null
        $bdd->exec("update etudiant set source_code = num_etu where source_code is null");
        $bdd->exec("alter table etudiant alter column source_code set not null");


        //Source id par défaut rendu + champ non null
        $bdd->exec(sprintf("update etudiant set source_id = (select id from source where code = '%s' ) where source_id is null", Source::STAGETREK));
        $bdd->exec("alter table etudiant alter column source_id set not null");

        $bdd->exec("update etudiant set histo_creation = now() where histo_creation is null");
        $bdd->exec("alter table etudiant alter column histo_creation set not null");


        $bdd->exec(sprintf("update etudiant set histo_createur_id = (select id from unicaen_utilisateur_user where username = '%s' ) where histo_createur_id is null", UserProvider::APP_USER));
        $bdd->exec("alter table etudiant alter column histo_createur_id set not null");


//        $bdd->exec("update faq_categorie_question set code = 'generale' where libelle='Générale'");
//        $bdd->exec('CREATE UNIQUE INDEX IF NOT EXISTS faq_categorie_code_unique ON "faq_categorie_question" ( code )');
//        $bdd->exec('alter table faq_categorie_question alter column code set not null');
    }

}