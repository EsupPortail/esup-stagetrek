<?php

namespace Application\Entity\Db;


use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ParametreCategorie
 */
class ParametreCategorie implements ResourceInterface
{
    const RESOURCE_ID = 'ParametreCategorie';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    const ID_CATEGORIE_PARAMETRES_GENERAUX = 1;
    const ID_CATEGORIE_PARAMETRES_STAGES = 2;
    const ID_CATEGORIE_PARAMETRES_ETUDIANTS = 3;
    const ID_CATEGORIE_PARAMETRES_ALGORITHMES = 4;
    const ID_CATEGORIE_PARAMETRES_MAILS = 5;
    const ID_CATEGORIE_PARAMETRES_CONVENTIONS = 6;


    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
