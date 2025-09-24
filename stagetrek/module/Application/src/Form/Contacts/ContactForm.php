<?php


namespace Application\Form\Contacts;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Contacts\Fieldset\ContactFieldset;

class ContactForm extends AbstractEntityForm
{
    public function init(): static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ContactFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
