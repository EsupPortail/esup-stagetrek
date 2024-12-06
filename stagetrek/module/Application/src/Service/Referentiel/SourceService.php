<?php

namespace Application\Service\Referentiel;

use Application\Entity\Db\Source;
use Application\Service\Misc\CommonEntityService;

class SourceService extends CommonEntityService
{
    public function getEntityClass() : string
    {
        return Source::class;
    }

    public function findAll(): array
    {
        return $this->getObjectRepository()->findBy([],['ordre'=>'asc']);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByCode(string $code): ?Source
    {
        return $this->getObjectRepository()->findOneBy(['code' => $code]);
    }

}