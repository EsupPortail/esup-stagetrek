<?php
namespace Application\Form\Affectation\Traits;

use Application\Form\Affectation\ProcedureAffectationForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits AffectationStageFormAwareTrait
 * @package Application\Form\AffectationStage\Traits
 */
trait  ProcedureAffectationFormAwareTrait
{
    /**
     * @var ProcedureAffectationForm|null $procedureAffectationForm ;
     */
    protected ?ProcedureAffectationForm $procedureAffectationForm = null;

    /**
     * @return ProcedureAffectationForm
     */
    public function getEditProcedureAffectationForm(): ProcedureAffectationForm
    {
        $form = $this->procedureAffectationForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ProcedureAffectationForm $procedureAffectationForm
     * @return ProcedureAffectationFormAwareTrait
     */
    public function setProcedureAffectationForm(ProcedureAffectationForm $procedureAffectationForm) : static
    {
        $this->procedureAffectationForm = $procedureAffectationForm;
        return $this;
    }
}