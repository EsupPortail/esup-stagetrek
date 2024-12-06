<?php


namespace Application\Form\Contacts\Element;


use Application\Entity\Db\Contact;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class ContactSelectPicker extends AbstractSelectPicker
{
    public function hasOptions(): bool
    {
        return !empty($this->getOption('options'));
    }

    public function setDefaultData() : static
    {
        $contacts = $this->getObjectManager()->getRepository(Contact::class)->findAll();
        usort($contacts, function (Contact $c1, Contact $c2) {
            if($c1->isActif() && ! $c2->isActif()) return 1;
            if($c2->isActif() && ! $c1->isActif()) return -1;
            return strcmp($c1->getDisplayName(), $c2->getDisplayName());
        });
        $this->setContacts($contacts);
        return $this;
    }

    public function setContacts($contacts): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($contacts as $c) {
            $this->addContact($c);
        }
        return $this;
    }

    public function addContact(Contact $contact): static
    {
        if ($this->hasContact($contact)) return $this;
        $label =  $contact->getDisplayName();
        if($contact->getEmail() !== ""){
            $label .= sprintf("<span class='mx-1 text-small text-muted'>(%s)</span>", $contact->getEmail());
        }
        $this->setContactOption($contact, 'label', $contact->getLibelle());
        $this->setContactAttribute($contact, 'data-content', $label);
        $this->setContactOption($contact, 'value', $contact->getId());
        return $this;
    }

    public function removeContact($contact): static
    {
        if (!$this->hasContact($contact))return $this;
        $options = $this->getOption('options');
        unset($options[$contact->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasContact($contact): bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($contact->getId(), $options));
    }

    public function getContactOptions($contact) : array
    {
        if (!$this->hasContact($contact)) return [];
        $options = $this->getOption('options');
        return $options[$contact->getId()];
    }

    public function getContactAttributes($contact)
    {
        $options = $this->getContactOptions($contact);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setContactOption($contact, $key, $value) : static
    {
        $options = $this->getContactOptions($contact);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$contact->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }


    public function setContactAttribute($contact, $key, $value) : static
    {
        $attributes = $this->getContactAttributes($contact);
        $attributes[$key] = $value;
        $this->setContactOption($contact, 'attributes', $attributes);
        return $this;
    }

}