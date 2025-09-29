<?php

namespace Application\Form\Etudiant\Traits;

use Application\Form\Etudiant\DisponibiliteForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits DisponibiliteFormAwareTrait
 * @package Application\Form\Disponibilite
 */
trait DisponibiliteFormAwareTrait
{
    /**
     * @var DisponibiliteForm|null $disponibiliteForm ;
     */
    protected ?DisponibiliteForm $disponibiliteForm = null;

    /**
     * @return DisponibiliteForm
     *
     */
    public function getAddDisponibiliteForm(): DisponibiliteForm
    {
        $form = $this->disponibiliteForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return DisponibiliteForm
     *
     */
    public function getEditDisponibiliteForm(): DisponibiliteForm
    {
        $form = $this->disponibiliteForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }


    /**
     * @param DisponibiliteForm $disponibiliteForm
     * @return \Application\Form\Etudiant\Traits\DisponibiliteFormAwareTrait
     */
    public function setDisponibiliteForm(DisponibiliteForm $disponibiliteForm) : static
    {
        $this->disponibiliteForm = $disponibiliteForm;
        return $this;
    }

}