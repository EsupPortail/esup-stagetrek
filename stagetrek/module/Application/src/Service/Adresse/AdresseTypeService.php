<?php

namespace Application\Service\Adresse;

use Application\Entity\Db\AdresseType;
use Application\Service\Misc\CommonEntityService;

class AdresseTypeService extends CommonEntityService
{

    public function getEntityClass(): string
    {
        return AdresseType::class;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByCode(string $code) : ?AdresseType
    {
        /** @var AdresseType $type */
        return $this->getObjectRepository()->findOneBy(['code' => $code]);
    }
}