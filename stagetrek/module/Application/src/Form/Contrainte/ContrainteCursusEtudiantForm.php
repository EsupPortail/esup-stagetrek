<?php


namespace Application\Form\Contrainte;

use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Contrainte\Fieldset\ContrainteCursusEtudiantFieldset;

/**
 * Class ContrainteCursusEtudiantForm
 * @package Application\Form\ContraintesCursus
 */
class ContrainteCursusEtudiantForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ContrainteCursusEtudiantFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
    public function setEtudiantContrainteCursus(ContrainteCursusEtudiant $contrainteCursusEtudiant) : static
    {
        /** @var ContrainteCursusEtudiantFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setContrainteCursusEtudiant($contrainteCursusEtudiant);
        return $this;
    }
}
