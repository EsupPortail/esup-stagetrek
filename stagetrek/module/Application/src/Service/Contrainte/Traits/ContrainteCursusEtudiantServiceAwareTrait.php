<?php
namespace Application\Service\Contrainte\Traits;

use Application\Service\Contrainte\ContrainteCursusEtudiantService;

Trait ContrainteCursusEtudiantServiceAwareTrait
{
    /** @var ContrainteCursusEtudiantService|null $contrainteCursusEtudiantService*/
    protected ?ContrainteCursusEtudiantService $contrainteCursusEtudiantService = null;

    /**
     * @return ContrainteCursusEtudiantService
     */
    public function getContrainteCursusEtudiantService(): ContrainteCursusEtudiantService
    {
        return $this->contrainteCursusEtudiantService;
    }

    /**
     * @param ContrainteCursusEtudiantService $contrainteCursusEtudiantService
     * @return \Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait
     */
    public function setContrainteCursusEtudiantService(ContrainteCursusEtudiantService $contrainteCursusEtudiantService): static
    {
        $this->contrainteCursusEtudiantService = $contrainteCursusEtudiantService;
        return $this;
    }
}