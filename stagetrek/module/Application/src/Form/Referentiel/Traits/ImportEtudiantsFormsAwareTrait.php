<?php


namespace Application\Form\Referentiel\Traits;

use Application\Form\Referentiel\Interfaces\ImportEtudiantsFormInterface;
use Application\Form\Referentiel\SourceForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ReferentielPromoFormAwareTrait
 * @package Application\Form\Traits
 */
trait ImportEtudiantsFormsAwareTrait
{
    /** @var ImportEtudiantsFormInterface[] $importEtudiantsForms  */
    protected array $importEtudiantsForms = [];

    public function getImportEtudiantsForms(): array
    {
        return $this->importEtudiantsForms;
    }

    public function setImportEtudiantsForms(array $importEtudiantsForms): static
    {
        $this->importEtudiantsForms = $importEtudiantsForms;
        return $this;
    }

    public function addImportEtudiantsForms(ImportEtudiantsFormInterface $form) : static
    {
        $this->importEtudiantsForms[$form->getKey()] = $form;
        return $this;
    }

    public function removeImportEtudiantsForms(?ImportEtudiantsFormInterface $form=null, string $key=null) : static
    {
       if(isset($form)) {
            $key = $form->getKey();
       }
        if(isset($this->importEtudiantsForms[$key])){
           unset($this->importEtudiantsForms[$key]);
        }
        return $this;
    }

    public function getImportEtudiantsForm(string $key) : ?ImportEtudiantsFormInterface
    {
        return ($this->importEtudiantsForms[$key])??null;
    }

}