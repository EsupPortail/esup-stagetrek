<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * AdresseType
 */
class AdresseType implements ResourceInterface //, CodeEntityInterface, LibelleEntityInterface
{
    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;

    const RESOURCE_ID = 'AdresseType';

    CONST TYPE_INCONNU = 'n/a';
    CONST TYPE_ETUDIANT = 'etu';
    CONST TYPE_TERRAIN_STAGE = 'terrain';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

//    public function generateDefaultCode(array $param = []) : string
//    {
//        $uid = ($param['uid']) ?? $this->getId();
//        if($uid == null){$uid = uniqid();}
//        $prefixe = ($param['prefixe']) ?? 'addrType';;
//        $separateur = ($param['sep']) ?? '#';
//        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
//    }
//
//    public function getCodePrefixe(): string
//    {
//        return "addr_type";
//    }
}
