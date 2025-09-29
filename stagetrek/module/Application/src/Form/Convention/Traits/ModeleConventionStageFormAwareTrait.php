<?php

namespace  Application\Form\Convention\Traits;

use Application\Form\Convention\ModeleConventionStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait ModeleConventionStageFormAwareTrait
{
    /**
     * @var ModeleConventionStageForm|null $modeleConventionStageForm ;
     */
    protected ?ModeleConventionStageForm $modeleConventionStageForm = null;

    /**
     * @return ModeleConventionStageForm
     */
    public function getModeleConventionStageForm(): ModeleConventionStageForm
    {
        return $this->modeleConventionStageForm;
    }

    /**
     * @param ModeleConventionStageForm $modeleConventionStageForm
     */
    public function setModeleConventionStageForm(ModeleConventionStageForm $modeleConventionStageForm): void
    {
        $this->modeleConventionStageForm = $modeleConventionStageForm;
    }


    /**
     * @return ModeleConventionStageForm
     *
     */
    public function getAddModeleConventionStageForm(): ModeleConventionStageForm
    {
        $form = $this->modeleConventionStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));

        return $form;
    }

    /**
     * @return ModeleConventionStageForm
     */
    public function getEditModeleConventionStageForm(): ModeleConventionStageForm
    {
        $form = $this->modeleConventionStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

}