<?php

namespace Application\Service\Referentiel;

use Application\Entity\Db\ReferentielPromo;
use Application\Service\Misc\CommonEntityService;

class ReferentielPromoService extends CommonEntityService
{

    public function getEntityClass(): string
    {
        return ReferentielPromo::class;
    }

    public function findAll(): array
    {
        return $this->getObjectRepository()->findBy([],['ordre'=>'asc']);
    }

    /**
     * @param string $code
     * @return \Application\Entity\Db\ReferentielPromo|null
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByCode(string $code): ?ReferentielPromo
    {
        return $this->getObjectRepository()->findOneBy(['code' => $code]);
    }
}