<?php

namespace BddAdmin\Migration;

use Application\Provider\Tag\TagProvider;
use Unicaen\BddAdmin\Ddl\Ddl;
use Unicaen\BddAdmin\Migration\MigrationAction;

class Migration_20251030_Session extends MigrationAction {

    public function description(): string
    {
        return "Inclusion du tag 'session rattrapage'";
    }

    // Retoure si le script doit être executé ou nom
    public function utile(): bool
    {
        if(!$this->manager()->hasTable('unicaen_tag')){
            return false;
        }
        if(!$this->manager()->hasTable('session_stage_tag_linker')){
            return false;
        }
        $bdd = $this->getBdd();
        $tagId = $bdd->selectOne(sprintf("select id from unicaen_tag where code='%s'", TagProvider::SESSION_RATTRAPAGE));
        if(empty($tagId)){return false;}
        $this->tagId = current($tagId);
        $sql = <<<EOS
            select DISTINCT ss.id as session_stage_id, l.tag_id from session_stage ss
                left join session_stage_tag_linker l on (ss.id, %s) = (l.session_stage_id, l.tag_id)
            where ss.session_rattrapage = true
            and l.tag_id is null
        EOS;
        $sql = sprintf($sql, $this->tagId);
        $this->sessions = $bdd->select($sql);
        return !empty($this->sessions);
    }

    protected array $sessions = [];
    protected int $tagId = 0;

    public function before():void
    {
    }

    public function after():void
    {
        $bdd = $this->getBdd();
        foreach($this->sessions as $session){
            $sessionId = $session['session_stage_id'];
            $sql = sprintf("insert into session_stage_tag_linker (session_stage_id, tag_id) VALUES (%s, %s)"
                ,$sessionId, $this->tagId);
            $bdd->exec($sql);
        }
//        foreach ($this->etudiantsViewRef as $row) {
//            $viewName = $row['view_name'];
//            $bdd->getLogger()->msg("Suppression de la vue ".$viewName);
//            $bdd->exec(sprintf("drop VIEW IF EXISTS %s", $viewName));
//        }
    }

}