<?php


namespace Application\Form\Notification;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Notification\Fieldset\MessageInfoFieldset;
use Application\Provider\Misc\Label;

/**
 * Class MessageInfoForm
 * @package Application\Form\Message
 */
class MessageInfoForm extends AbstractEntityForm
implements Label
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(MessageInfoFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
