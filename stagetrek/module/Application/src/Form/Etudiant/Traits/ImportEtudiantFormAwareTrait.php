<?php


namespace Application\Form\Etudiant\Traits;

use Application\Entity\Db\Groupe;
use Application\Form\Etudiant\ImportEtudiantForm;

/**
 * Traits ImportEtudiantFormAwareTrait
 * @package Application\Form\Etudiant\Traits
 * @deprecated Refonte en cours
 */
trait ImportEtudiantFormAwareTrait
{
    /** @var ImportEtudiantForm|null */
    protected ?ImportEtudiantForm $importEtudiantForm = null;

    /**
     * @param \Application\Entity\Db\Groupe|null $groupe
     * @return ImportEtudiantForm
     */
    public function getImportEtudiantForm(?Groupe $groupe=null): ImportEtudiantForm
    {
        if ($groupe != null) {
            $this->importEtudiantForm->fixeGroupe($groupe);
        }
        return $this->importEtudiantForm;
    }

    public function setImportEtudiantForm(ImportEtudiantForm $importEtudiantForm) : static
    {
        $this->importEtudiantForm = $importEtudiantForm;
        return $this;
    }
}