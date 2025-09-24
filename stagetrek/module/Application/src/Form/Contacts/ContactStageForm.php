<?php


namespace Application\Form\Contacts;

use Application\Entity\Db\Contact;
use Application\Entity\Db\Stage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Contacts\Fieldset\ContactStageFieldset;

class ContactStageForm extends AbstractEntityForm
{
    public function init(): static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ContactStageFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

    public function setModeEdition(?bool $mode = true) : static
    {
        /** @var ContactStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setModeEdition($mode);
        return $this;
    }

    public function isModeEdition() : bool
    {
        /** @var ContactStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        return $fieldset->isModeEdition();
    }

    public function setContact(Contact $contact): static
    {
        /** @var ContactStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setContact($contact);
        return $this;
    }
    public function setStage(Stage $stage) : static
    {
        /** @var ContactStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setStage($stage);
        return $this;
    }
}
