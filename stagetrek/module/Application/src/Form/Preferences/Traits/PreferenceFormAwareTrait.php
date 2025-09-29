<?php

namespace Application\Form\Preferences\Traits;


use Application\Form\Preferences\PreferenceForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits PreferenceFormAwareTrait
 * @package Application\Form\Traits
 */
trait  PreferenceFormAwareTrait
{
    /**
     * @var PreferenceForm|null $preferenceForm ;
     */
    protected ?PreferenceForm $preferenceForm = null;

    /**
     * @return PreferenceForm
     */
    public function getAddPreferenceForm(): PreferenceForm
    {
        $form = $this->preferenceForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return PreferenceForm
     */
    public function getEditPreferenceForm(): PreferenceForm
    {
        $form = $this->preferenceForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param PreferenceForm $preferenceForm
     * @return \Application\Form\Preferences\Traits\PreferenceFormAwareTrait
     */
    public function setPreferenceForm(PreferenceForm $preferenceForm) : static
    {
        $this->preferenceForm = $preferenceForm;
        return $this;
    }

}