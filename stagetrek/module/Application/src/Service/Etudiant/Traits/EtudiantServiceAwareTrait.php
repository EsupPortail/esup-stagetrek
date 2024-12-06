<?php


namespace Application\Service\Etudiant\Traits;

use Application\Service\Etudiant\EtudiantImportService;
use Application\Service\Etudiant\EtudiantService;

/**
 * Traits EtudiantServiceAwareTrait
 * @package Application\Service\Etudiant
 */
trait EtudiantServiceAwareTrait
{
    /**
     * @var EtudiantService|null $etudiantService
     */
    protected ?EtudiantService $etudiantService=null;

    /**
     * @return EtudiantService
     */
    public function getEtudiantService(): EtudiantService
    {
        return $this->etudiantService;
    }

    /**
     * @param EtudiantService $etudiantService
     * @return \Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait
     */
    public function setEtudiantService(EtudiantService $etudiantService): static
    {
        $this->etudiantService = $etudiantService;
        return $this;
    }

    /**
     * @var EtudiantImportService|null $etudiantImportService
     */
    protected ?EtudiantImportService $etudiantImportService = null;

    public function getEtudiantImportService(): EtudiantImportService
    {
        return $this->etudiantImportService;
    }

    public function setEtudiantImportService(EtudiantImportService $etudiantImportService): static
    {
        $this->etudiantImportService = $etudiantImportService;
        return $this;
    }


}
