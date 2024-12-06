<?php

namespace Application\Form\Notification;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Notification\Fieldset\FaqQuestionFieldset;

/**
 * Class FaqQuestionForm
 * @package Application\Form
 */
class FaqQuestionForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(FaqQuestionFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}
