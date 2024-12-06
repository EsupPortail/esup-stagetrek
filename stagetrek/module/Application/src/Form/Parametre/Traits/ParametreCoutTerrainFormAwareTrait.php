<?php


namespace Application\Form\Parametre\Traits;

use Application\Form\Parametre\ParametreCoutTerrainForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ParametreFormAwareTrait
 * @package Application\Form\Traits
 */
trait  ParametreCoutTerrainFormAwareTrait
{

    /**
     * @var ParametreCoutTerrainForm|null $parametreTerrainCoutAffectationFixeForm ;
     */
    protected ?ParametreCoutTerrainForm $parametreTerrainCoutAffectationFixeForm = null;

    /**
     * @return ParametreCoutTerrainForm
     */
    public function getAddParametreTerrainCoutAffectationFixeForm(): ParametreCoutTerrainForm
    {
        $form = $this->parametreTerrainCoutAffectationFixeForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return ParametreCoutTerrainForm
     */
    public function getEditParametreCoutTerrainForm(): ParametreCoutTerrainForm
    {
        $form = $this->parametreTerrainCoutAffectationFixeForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ParametreCoutTerrainForm $parametreTerrainCoutAffectationFixeForm
     * @return ParametreCoutTerrainFormAwareTrait
     */
    public function setParametreCoutTerrainForm(ParametreCoutTerrainForm $parametreTerrainCoutAffectationFixeForm): static
    {
        $this->parametreTerrainCoutAffectationFixeForm = $parametreTerrainCoutAffectationFixeForm;
        return $this;
    }

}