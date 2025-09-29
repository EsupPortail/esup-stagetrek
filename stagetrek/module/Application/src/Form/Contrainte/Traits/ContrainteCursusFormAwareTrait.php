<?php


namespace Application\Form\Contrainte\Traits;

use Application\Form\Contrainte\ContrainteCursusEtudiantForm;
use Application\Form\Contrainte\ContrainteCursusForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ContrainteCursusFormAwareTrait
 * @package Application\Form\ContraintesCursus\Traits
 */
trait  ContrainteCursusFormAwareTrait
{
    /**
     * @var ContrainteCursusForm|null $contrainteCursusForm ;
     */
    protected ?ContrainteCursusForm $contrainteCursusForm = null;

    /**
     * @var ContrainteCursusEtudiantForm|null $contrainteCursusEtudiantForm ;
     */
    protected ?ContrainteCursusEtudiantForm $contrainteCursusEtudiantForm = null;

    /**
     * @return ContrainteCursusForm
     */
    public function getAddContrainteCursusForm(): ContrainteCursusForm
    {
        $form = $this->contrainteCursusForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return ContrainteCursusForm
     */
    public function getEditContrainteCursusForm(): ContrainteCursusForm
    {
        $form = $this->contrainteCursusForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ContrainteCursusForm $contrainteCursusForm
     * @return \Application\Form\Contrainte\Traits\ContrainteCursusFormAwareTrait
     */
    public function setContrainteCursusForm(ContrainteCursusForm $contrainteCursusForm): static
    {
        $this->contrainteCursusForm = $contrainteCursusForm;
        return $this;
    }

    /**
     * @return ContrainteCursusEtudiantForm
     */
    public function getContrainteCursusEtudiantForm(): ContrainteCursusEtudiantForm
    {
        return $this->contrainteCursusEtudiantForm;
    }

    /**
     * @param ContrainteCursusEtudiantForm $etudiantCntrainteCursusForm
     * @return \Application\Form\Contrainte\Traits\ContrainteCursusFormAwareTrait
     */
    public function setContrainteCursusEtudiantForm(ContrainteCursusEtudiantForm $etudiantCntrainteCursusForm): static
    {
        $this->contrainteCursusEtudiantForm = $etudiantCntrainteCursusForm;
        return $this;
    }


}