<?php

namespace BddAdmin\Migration;

use Unicaen\BddAdmin\Ddl\Ddl;
use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20250924_Etudiant extends MigrationAction {

    public function description(): string
    {
        return "Changement du type de l'ID des étudiants";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        if(!$this->manager()->hasTable('etudiant')){
            return false;
        }
        $oldEtu = $this->manager()->getOld()->get(Ddl::TABLE)['etudiant'];
        $newEtu = $this->manager()->getRef()->get(Ddl::TABLE)['etudiant'];
        $oldIdType = ($oldEtu['columns']['id']['bdd-type']) ?? null;
        $newIdType = ($newEtu['columns']['id']['bdd-type']) ?? null;
        if(!isset($oldIdType) || !isset($newIdType) || $oldIdType == $newIdType){
            return false;
        }

        $bdd = $this->getBdd();
        $sql = <<<EOS
select
	u.view_name,
	u.table_name referenced_table_name
from information_schema.view_table_usage u
	     join information_schema.views v on u.view_schema = v.table_schema
    and u.view_name = v.table_name
where u.table_schema not in ('information_schema', 'pg_catalog')
    and u.table_name = 'etudiant'
EOS;
        $this->etudiantsViewRef = $bdd->select($sql);
        return !empty($this->etudiantsViewRef);
    }

    protected array $etudiantsViewRef = [];

    public function before():void
    {
        $bdd = $this->getBdd();
        foreach ($this->etudiantsViewRef as $row) {
            $viewName = $row['view_name'];
            $bdd->getLogger()->msg("Suppression de la vue ".$viewName);
            $bdd->exec(sprintf("drop VIEW IF EXISTS %s", $viewName));
        }
    }

    public function after():void
    {
    }

}