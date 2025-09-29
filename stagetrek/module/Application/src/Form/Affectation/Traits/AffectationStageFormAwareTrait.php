<?php
namespace Application\Form\Affectation\Traits;


use Application\Form\Affectation\AffectationStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits AffectationStageFormAwareTrait
 * @package Application\Form\AffectationStage\Traits
 */
trait  AffectationStageFormAwareTrait
{
    /**
     * @var AffectationStageForm|null $affectationStageForm ;
     */
    protected ?AffectationStageForm $affectationStageForm = null;

    /**
     * @return AffectationStageForm
     */
    public function getAddAffectationStageForm(): AffectationStageForm
    {
        $form = $this->affectationStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AFFECTER, Icone::CHECK));
        return $form;
    }

    /**
     * @return AffectationStageForm
     */
    public function getEditAffectationStageForm(): AffectationStageForm
    {
        $form = $this->affectationStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));

        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param AffectationStageForm $affectationStageForm
     * @return \Application\Form\Affectation\Traits\AffectationStageFormAwareTrait
     */
    public function setAffectationStageForm(AffectationStageForm $affectationStageForm) : static
    {
        $this->affectationStageForm = $affectationStageForm;
        return $this;
    }
}