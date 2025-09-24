<?php

namespace Application\Service\Referentiel\Traits;


use Application\Service\Referentiel\Interfaces\ReferentielEtudiantServiceInterface;

trait ReferentielsEtudiantsServicesAwareTrait
{
    /** @var ReferentielEtudiantServiceInterface[] */
    protected array|null $referentielsEtudiantsServices = [];

    public function getReferentielsEtudiantsServices(): array
    {
        return $this->referentielsEtudiantsServices;
    }

    public function setReferentielsEtudiantsServices(array $service): static
    {
        $this->referentielsEtudiantsServices = $service;
        return $this;
    }

    public function addReferentielEtudiantService(ReferentielEtudiantServiceInterface $service, ?string $key=null) : static
    {
        $key = ($key) ?? $service->getKey();
        $this->referentielsEtudiantsServices[$key] = $service;
        return $this;
    }

    public function removeReferentielsEtudiantsServices(?ReferentielEtudiantServiceInterface $service=null, ?string $key=null) : static
    {
        $key = ($key) ?? $service->getKey();
        if(isset($this->referentielsEtudiantsServices[$key])){
            unset($this->referentielsEtudiantsServices[$key]);
        }
        return $this;
    }

    public function getReferentielEtudiantService(string $key) : ?ReferentielEtudiantServiceInterface
    {
        return ($this->referentielsEtudiantsServices[$key])??null;
    }

}