<?php
namespace Application\Service\Contrainte\Traits;

use Application\Service\Contrainte\ContrainteCursusService;

Trait ContrainteCursusServiceAwareTrait
{
    /** @var ContrainteCursusService|null $contrainteCursusService*/
    protected ?ContrainteCursusService $contrainteCursusService = null;

    /**
     * @return ContrainteCursusService
     */
    public function getContrainteCursusService() : ContrainteCursusService{
        return $this->contrainteCursusService;
    }

    /**
     * @param ContrainteCursusService $contrainteCursusService
     * @return \Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait
     */
    public function setContrainteCursusService(ContrainteCursusService $contrainteCursusService): static
    {
        $this->contrainteCursusService = $contrainteCursusService;
        return $this;
    }
}