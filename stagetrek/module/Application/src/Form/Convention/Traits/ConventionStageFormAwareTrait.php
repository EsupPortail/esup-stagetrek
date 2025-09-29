<?php

namespace  Application\Form\Convention\Traits;

use Application\Form\Convention\ConventionStageTeleversementForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait ConventionStageFormAwareTrait
{
    protected ?ConventionStageTeleversementForm $conventionStageTeleversementForm = null;

    public function getConventionStageTeleversementForm(): ?ConventionStageTeleversementForm
    {
        $form = $this->conventionStageTeleversementForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::TELEVERSER, Icone::TELEVERSER));
        return $form;
    }

    public function setConventionStageTeleversementForm(?ConventionStageTeleversementForm $conventionStageTeleversementForm): static
    {
        $this->conventionStageTeleversementForm = $conventionStageTeleversementForm;
        return $this;
    }


}