<?php

namespace Application\Service\Parametre;

use Application\Entity\Db\NiveauEtude;
use Application\Service\Misc\CommonEntityService;

/**
 * Class NiveauEtudeService
 * @package Application\Service\AnneeUniversitaire
 */
class NiveauEtudeService extends CommonEntityService
{

    /** @return string */
    public function getEntityClass(): string
    {
        return NiveauEtude::class;
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAll(): array
    {
        return $this->findAllBy([], ['ordre' => 'ASC']);
    }
}