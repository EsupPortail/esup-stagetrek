<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * AdresseType
 */
class AdresseType implements ResourceInterface //, CodeEntityInterface, LibelleEntityInterface
{
    use IdEntityTrait;
    use CodeEntityTrait;
    use LibelleEntityTrait;

    const RESOURCE_ID = 'AdresseType';


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
