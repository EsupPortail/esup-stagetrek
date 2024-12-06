<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\AcronymeEntityInterface;
use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\AcronymeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Application\Entity\Traits\Terrain\HasTerrainsStagesTrait;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * CategorieStage
 */
class CategorieStage implements ResourceInterface,
    LibelleEntityInterface, CodeEntityInterface, AcronymeEntityInterface
{

    const RESOURCE_ID = 'CategorieStage';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? $this->getId();
        if($uid == null){$uid = uniqid();}
        if(isset($param['prefixe'])){$prefixe = $param['prefixe'];}
        elseif ($this->getAcronyme() === null || $this->getAcronyme()==""){$prefixe = 'CS';}
        else{$prefixe = strtolower(substr(trim($this->getAcronyme()), 0,10));}
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initTerrainsStagesCollection();
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use AcronymeEntityTrait;
    use CodeEntityTrait;


    use HasTerrainsStagesTrait;

    use OrderEntityTrait;
    //Surchage de la fonction de trie pour gérer la distincting Catégorie Principal/secondaire
    public static function sort(array|Collection $entities, string $order = 'asc'): array
    {
        $ordre = ($order != 'desc') ? 1 : -1;
        if($entities instanceof Collection){$entities = $entities->toArray();}
        usort($entities, function (CategorieStage $c1, CategorieStage $c2) use($ordre) {
            if($c1->isCategoriePrincipale() && !$c2->isCategoriePrincipale()){ return -$ordre;}
            if($c2->isCategoriePrincipale() && !$c1->isCategoriePrincipale()){ return $ordre;}
            if ($c1->getOrdre() - $c2->getOrdre() != 0) {
                return $ordre*($c1->getOrdre() - $c2->getOrdre());
            }
            return $ordre*strcmp($c1->getLibelle(), $c2->getLibelle());
        });
        return $entities;
    }


    /**
     * @var bool
     */
    protected bool $categoriePrincipale = true;

    /**
     * @return bool
     */
    public function isCategoriePrincipale(): bool
    {
        return $this->categoriePrincipale;
    }    /**
     * @return bool
     */
    public function isCategorieSecondaire(): bool
    {
        return !$this->categoriePrincipale;
    }

    /**
     * @param bool $categoriePrincipale
     * @return \Application\Entity\Db\CategorieStage
     */
    public function setCategoriePrincipale(bool $categoriePrincipale) : static
    {
        $this->categoriePrincipale = $categoriePrincipale;
        return $this;
    }

    /**
     * Vrais si tout les terrains de la catégorie sont contraintes par le niveau d'étude
     * @param NiveauEtude $niveauEtude
     * @return bool
     */
    public function isContraintForNiveauEtude(NiveauEtude $niveauEtude): bool
    {
        /** @var TerrainStage $t */
        foreach ($this->getTerrainsStages() as $t){
            if(!$t->isContraintForNiveauEtude($niveauEtude)){
                return false;
            }
        }
        return true;
    }
}
