<?php

namespace Application\Entity\Traits\InterfaceImplementation;

use Application\Provider\Tag\TagProvider;
use Exception;
use UnicaenTag\Entity\Db\HasTagsTrait;
use UnicaenTag\Entity\Db\Tag;

trait HasLockTrait
{
    use HasTagsTrait;

    /**
     * @throws \Exception
     */
    public function lock(... $param) : static
    {
        $tag = ($param[0]) ?? null;
        if(!$tag instanceof Tag || $tag->getCode() != TagProvider::ETAT_LOCK){
            throw new Exception("Le tag pour vérrouiller l'entité n'est pas valide");
        }
        if($this->isLocked()){return $this;}
        $this->addTag($tag) ;
        return $this;
    }
    public function unlock() : static
    {
        if(!$this->isLocked()){return $this;}
        $tag = $this->getTagWithCode(TagProvider::ETAT_LOCK);
        $this->removeTag($tag);
        return $this;
    }
    public function isLocked() : bool
    {
        return $this->hasTagWithCode(TagProvider::ETAT_LOCK);
    }
}