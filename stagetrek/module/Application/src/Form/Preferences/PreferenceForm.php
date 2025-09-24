<?php

namespace Application\Form\Preferences;

use Application\Entity\Db\Preference;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;

/**
 * Class PreferenceForm
 * @package Application\Form
 */
class PreferenceForm extends AbstractEntityForm
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(PreferenceFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

    public function setModeAdmin(bool $modeAdmin=true): void
    {
        /** @var PreferenceFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setModeAdmin($modeAdmin);
    }

    public function getModeAdmin() : bool
    {
        /** @var PreferenceFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        return $fieldset->getModeAdmin();
    }

    /**
     * @return Preference
     */
    public function getPreference(): Preference
    {
        /** @var PreferenceFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        return $fieldset->getPreference();
    }

    /**
     * @param \Application\Entity\Db\Preference $preference
     * @return \Application\Form\Preferences\PreferenceForm
     */
    public function setPreference(Preference $preference): static
    {
        /** @var PreferenceFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setPreference($preference);
        return $this;
    }


}