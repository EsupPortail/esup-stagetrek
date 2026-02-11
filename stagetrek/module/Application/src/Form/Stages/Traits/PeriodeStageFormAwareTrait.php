<?php


namespace Application\Form\Stages\Traits;

use Application\Entity\Db\SessionStage;
use Application\Form\Stages\PeriodeStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait PeriodeStageFormAwareTrait
{
    /**
     * @var PeriodeStageForm|null $periodeStageForm;
     */
    protected ?PeriodeStageForm $periodeStageForm = null;

    /**
     * @return PeriodeStageForm
     */
    public function getAddPeriodeStageForm(SessionStage $session): PeriodeStageForm
    {
        $form = $this->periodeStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        $form->setSessionStage($session);
        return $form;
    }

    /**
     * @return PeriodeStageForm
     */
    public function getEditPeriodetageForm(SessionStage $session): PeriodeStageForm
    {
        $form = $this->periodeStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        $form->setSessionStage($session);
        return $form;
    }

    /**
     * @param PeriodeStageForm $periodeStageForm
     * @return PeriodeStageFormAwareTrait
     */
    public function setPeriodeStageForm(PeriodeStageForm $periodeStageForm): static
    {
        $this->periodeStageForm = $periodeStageForm;
        return $this;
    }
}