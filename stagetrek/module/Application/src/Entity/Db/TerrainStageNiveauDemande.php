<?php

namespace Application\Entity\Db;


use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

//TODO : gestions en backoffice ?
class TerrainStageNiveauDemande implements ResourceInterface,
    LibelleEntityInterface, OrderEntityInterface, CodeEntityInterface
{
    const RESOURCE_ID = 'TerrainStageNiveauDemande';

    const RANG_1 = 'rang_1';
    const RANG_2 = 'rang_2';
    const RANG_3 = 'rang_3';
    const RANG_4 = 'rang_4';
    const RANG_5 = 'rang_5';
    const NO_DEMANDE = 'no_demande';
    const FERME = 'ferme';
    const INDETERMINE = 'n/a';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }


    use CodeEntityTrait;
    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? uniqid();
        $prefixe = ($param['prefixe']) ?? 'niveau';;
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
