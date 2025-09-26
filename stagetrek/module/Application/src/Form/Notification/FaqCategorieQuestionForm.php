<?php


namespace Application\Form\Notification;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Notification\Fieldset\FaqCategorieQuestionFieldset;

/**
 * Class FaqCategorieQuestionForm
 * @package Application\Form
 */
class FaqCategorieQuestionForm extends AbstractEntityForm
{
    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(FaqCategorieQuestionFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
