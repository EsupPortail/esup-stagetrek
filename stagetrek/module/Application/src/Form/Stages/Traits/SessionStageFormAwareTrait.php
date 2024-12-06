<?php


namespace Application\Form\Stages\Traits;

use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Form\Stages\SessionStageForm;
use Application\Form\Stages\SessionStageRechercheForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits SessionStageFormAwareTrait
 * @package Application\Form\SessionsStages\Traits
 */
trait SessionStageFormAwareTrait
{
    /**
     * @var SessionStageForm|null $sessionStageForm;
     * @var SessionStageRechercheForm|null $sessionStageRechercheForm;
     */
    protected ?SessionStageForm $sessionStageForm = null;
    protected ?SessionStageRechercheForm $sessionStageRechercheForm = null;

    /**
     * @return SessionStageForm
     */
    public function getAddSessionStageForm(): SessionStageForm
    {
        $form = $this->sessionStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        $form->getBaseFieldset()->get(SessionStageFieldset::INPUT_CALCUL_AUTOMATIQUE_DATE)->setValue(true);
        return $form;
    }

    /**
     * @return SessionStageForm
     */
    public function getEditSessionStageForm(): SessionStageForm
    {
        $form = $this->sessionStageForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param SessionStageForm $sessionStageForm
     * @return SessionStageFormAwareTrait
     */
    public function setSessionStageForm(SessionStageForm $sessionStageForm): static
    {
        $this->sessionStageForm = $sessionStageForm;
        return $this;
    }

    /**
     * @return SessionStageRechercheForm
     */
    public function getSessionStageRechercheForm() : SessionStageRechercheForm
    {
        return $this->sessionStageRechercheForm;
    }

    /**
     * @param SessionStageRechercheForm $sessionStageRechercheForm
     * @return SessionStageFormAwareTrait
     */
    public function setSessionStageRechercheForm(SessionStageRechercheForm $sessionStageRechercheForm): static
    {
        $this->sessionStageRechercheForm = $sessionStageRechercheForm;
        return $this;
    }
}