<?php

namespace Application\Service\Parametre;


use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCategorie;
use Application\Service\Misc\CommonEntityService;
use Exception;
class ParametreService extends CommonEntityService
{

    /** @return string */
    public function getEntityClass(): string
    {
        return Parametre::class;
    }

    /**
     * @param string $code identifiant de l'entité
     * @return Parametre
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function findByCode(string $code) : Parametre
    {
        /** @var Parametre $param */
        $param = $this->getObjectRepository()->findOneBy(['code' => $code]);
        if(!$param){
            throw new Exception("Le paramètre de code ".$code." n'existe pas");
        }
        return $param;
    }


    /** Fonctions pour avoir directement les entités
     * @throws \Doctrine\ORM\Exception\NotSupported
     */

    public function getParametreValue(string $code) : mixed
    {
        $param = $this->findByCode($code);
        if(!$param->getParametreType()){return $param->getValue();}
        $castFonction = $param->getParametreType()->getCastFonction();
        if(!$castFonction){return $param->getValue();}
        return $castFonction($param->getValue());
    }


    /****************************************
     * @throws \Doctrine\ORM\Exception\NotSupported
     */

    public function findParametresCategories(): array
    {
        return $this->getObjectManager()->getRepository(ParametreCategorie::class)->findAll();
    }
}