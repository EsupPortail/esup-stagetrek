<?php

namespace Application\Entity\Traits\InterfaceImplementation;

//Trait générique permettant de ne pas avoir à réimplémenter les attributs/accesseur de base
use Application\Entity\Interfaces\HasCodeInterface;

trait HasCodeTrait
{
    /** @var string|null $code */
    protected ?string $code=null;

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return \Application\Entity\Traits\InterfaceImplementation\HasCodeTrait
     */
    public function setCode(?string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function hasCode(string $code): bool
    {
        return strcmp($this->code, $code)==0;
    }

    /*
     * Métode par défaut généric pour ne pas avoir de pb : on prend les 10 premier caractere de la classe en lowercase que l'on rattache soit a l'id s'il existe, soit a un nombre aléatoire
     */
    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? uniqid();
        $prefixe = ($param['prefixe']) ?? strtolower(substr(self::RESOURCE_ID, 0,4));;
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }


//if($entity instanceof TerrainStage){
////            Choix fait : prefixe = acronyme de la catégorie
//    $cat = $entity->getCategorieStage();
//    $acc = $cat->getAcronyme();
//    $prefixe = strtolower(trim($acc));
//    $param['prefixe'] = $prefixe;
//}
//if($entity instanceof TerrainStage){
////            Choix fait : prefixe = acronyme de la catégorie
//    $cat = $entity->getCategorieStage();
//    $acc = $cat->getAcronyme();
//    $prefixe = strtolower(trim($acc));
//    $param['prefixe'] = $prefixe;
//}
}