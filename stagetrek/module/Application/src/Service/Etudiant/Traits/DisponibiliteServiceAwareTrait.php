<?php

namespace Application\Service\Etudiant\Traits;



use Application\Service\Etudiant\DisponibiliteService;

/**
 * Traits DisponibiliteServiceAwareTrait
 * @package Application\Service\Traits
 */
trait DisponibiliteServiceAwareTrait
{
    /**
     * @var DisponibiliteService|null $disponibiliteService
     */
    protected ?DisponibiliteService $disponibiliteService=null;

    /**
     * @return DisponibiliteService
     */
    public function getDisponibiliteService(): DisponibiliteService
    {
        return $this->disponibiliteService;
    }

    /**
     * @param DisponibiliteService $disponibiliteService
     */
    public function setDisponibiliteService(DisponibiliteService $disponibiliteService) : static
    {
        $this->disponibiliteService = $disponibiliteService;
        return $this;
    }
}