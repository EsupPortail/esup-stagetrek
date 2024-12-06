<?php

namespace Console\Service\BddAdmin\Migration;
use Unicaen\BddAdmin\Migration\MigrationAction;

class MigrationTest extends MigrationAction {

    public function description(): string
    {
        return "Scrite de test";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        return true;
    }

    public function before():void
    {
        var_dump("Commande exectuer avant");
    }

    public function after():void
    {
        var_dump("Commande executer après");
    }
    // A voir : getBdd() : accés a la bdd
    // getManager() : Cf MigrationManager

    //Before : Commande a exectuer avant Toute modification
    //After : Commande a exectuer aprés toutes modification en base


}