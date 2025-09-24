<?php

namespace Application\Entity\Db;


use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

//TODO : gestions en backoffice ?
class TerrainStageNiveauDemande implements ResourceInterface,
    HasLibelleInterface, HasOrderInterface, HasCodeInterface
{
    const RESOURCE_ID = 'TerrainStageNiveauDemande';

    const RANG_1 = 'rang_1';
    const RANG_2 = 'rang_2';
    const RANG_3 = 'rang_3';
    const RANG_4 = 'rang_4';
    const RANG_5 = 'rang_5';
    const RANG_6 = 'rang_6';
    const RANG_7 = 'rang_7';
    const RANG_8 = 'rang_8';
    const RANG_9 = 'rang_9';
    const RANG_10 = 'rang_10';
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


    use HasCodeTrait;
    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? uniqid();
        $prefixe = ($param['prefixe']) ?? 'niveau';;
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }

    use HasIdTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
}
