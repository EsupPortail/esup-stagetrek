<?php

namespace BddAdmin\Migration;

use Unicaen\BddAdmin\Bdd;
use Unicaen\BddAdmin\Ddl\Ddl;
use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20250924_Annee extends MigrationAction {

    public function description(): string
    {
        return "Ajout des tags sur les années universitaire";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        return $this->manager()->hasNew(Ddl::TABLE, 'annee_tag_linker');
    }

    public function before():void
    {
    }

    public function after():void
    {
        $bdd = $this->getBdd();

        # ajout du tag lock aux années universitaires vérouillée
        $sql = <<<EOS
INSERT into annee_tag_linker (annee_universitaire_id, tag_id)
    (SELECT a.id AS annee_id, t.id AS tag_id  FROM
                           annee_universitaire a, unicaen_tag t
    WHERE a.annee_verrouillee = TRUE
    AND t.code ='lock' )
ON CONFLICT do NOTHING ;
EOS;
        $bdd->exec($sql);
    }

}