<?php


namespace Application\Form\Stages;

use Application\Entity\Db\SessionStage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Abstrait\Interfaces\AbstractFormConstantesInterface;
use Application\Form\Stages\Fieldset\PeriodeStageFieldset;

/**
 * @package Application\Form\SessionsStages
 */
class PeriodeStageForm extends AbstractEntityForm implements AbstractFormConstantesInterface
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(PeriodeStageFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

    // Fonction qui permet de fixer l'année universitaire selectionnée / le groupe


    public function setSessionStage(SessionStage $sessionStage) : static
    {
        /** @var PeriodeStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setSessionStage($sessionStage);
        return $this;
    }
}