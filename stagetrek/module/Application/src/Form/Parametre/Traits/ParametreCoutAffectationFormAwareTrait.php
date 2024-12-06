<?php


namespace Application\Form\Parametre\Traits;

use Application\Form\Parametre\ParametreCoutAffectationForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ParametreFormAwareTrait
 * @package Application\Form\Traits
 */
trait  ParametreCoutAffectationFormAwareTrait
{

    /**
     * @var ParametreCoutAffectationForm|null $parametreCoutAffectationForm ;
     */
    protected ?ParametreCoutAffectationForm $parametreCoutAffectationForm = null;

    /**
     * @return ParametreCoutAffectationForm
     */
    public function getAddParametreCoutAffectationForm(): ParametreCoutAffectationForm
    {
        $form = $this->parametreCoutAffectationForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return ParametreCoutAffectationForm
     */
    public function getEditParametreCoutAffectationForm(): ParametreCoutAffectationForm
    {
        $form = $this->parametreCoutAffectationForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ParametreCoutAffectationForm $parametreCoutAffectationForm
     * @return ParametreCoutAffectationFormAwareTrait
     */
    public function setParametreCoutAffectationForm(ParametreCoutAffectationForm $parametreCoutAffectationForm): static
    {
        $this->parametreCoutAffectationForm = $parametreCoutAffectationForm;
        return $this;
    }
}