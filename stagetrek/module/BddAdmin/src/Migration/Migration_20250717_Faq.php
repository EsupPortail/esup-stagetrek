<?php

namespace BddAdmin\Migration;

use Unicaen\BddAdmin\Migration\MigrationAction;
use Faker\Factory as FakerFactory;

class Migration_20250717_Faq extends MigrationAction {

    public function description(): string
    {
        return "Ajout du code sur les catégories de FAQ";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        return $this->manager()->hasTable('faq_categorie_question') && !$this->manager()->hasColumn('faq_categorie_question', 'code');
    }

    public function before():void
    {
        $bdd = $this->getBdd();
        $this->manager()->sauvegarderTable('faq_categorie_question', 'save_faq_categorie_question');
        $bdd->exec("ALTER TABLE faq_categorie_question add COLUMN code varchar(25)");
        $bdd->exec("update faq_categorie_question set code = 'cat_'||id");
        $bdd->exec("update faq_categorie_question set code = 'generale' where libelle='Générale'");
        $bdd->exec('CREATE UNIQUE INDEX IF NOT EXISTS faq_categorie_code_unique ON "faq_categorie_question" ( code )');
        $bdd->exec('alter table faq_categorie_question alter column code set not null');
        $this->manager()->supprimerSauvegarde('save_faq_categorie_question');

    }

    public function after():void
    {
    }

}