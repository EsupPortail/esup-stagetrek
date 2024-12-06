<?php


namespace Application\Form\Stages;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Stages\Fieldset\ValidationStageFieldset;
use Laminas\Form\Element\Csrf;

/**
 * Class ValidationStageForm
 * @package Application\Form\Stages
 */
class ValidationStageForm extends AbstractEntityForm
{

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ValidationStageFieldset::class);
        $this->setEntityFieldset($fieldset);
    }

    protected function initCSRF() : void
    {
        $this->add([
            'type' => Csrf::class,
            'name' => self::CSRF,
            'options' => [
                'csrf_options' => [ //on laisse 15 minutes afin de permettre les modifications tanquillement
                    'timeout' => 60*15,
                ],
            ],
        ]);
    }


    public function setModeAdmin(?bool $modeAdmin) : static
    {
        /** @var ValidationStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setModeAdmin($modeAdmin);
        return $this;
    }

    public function setValidateBy(?string $validateBy) : static
    {
        /** @var ValidationStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setValidateBy($validateBy);
        return $this;
    }
}
