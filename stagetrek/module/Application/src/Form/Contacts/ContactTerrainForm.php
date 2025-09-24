<?php


namespace Application\Form\Contacts;

use Application\Entity\Db\ContactTerrain;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Contacts\Fieldset\ContactTerrainFieldset;

class ContactTerrainForm extends AbstractEntityForm
{
    public function init(): static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ContactTerrainFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

    public function setModeEdition($mode = true): static
    {
        /** @var ContactTerrainFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setModeEdition($mode);
        return $this;
    }

    public function setContactTerrain(ContactTerrain $contactTerrain) : static
    {
        /** @var ContactTerrainFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setContactTerrain($contactTerrain);
        $this->bind($contactTerrain);
        return $this;
    }
}
