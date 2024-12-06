<?php


namespace Application\Form\Contrainte\Element;

use Application\Entity\Db\ContrainteCursus;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class ContrainteCursusSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $contraintes = $this->getObjectManager()->getRepository(ContrainteCursus::class)->findAll();
        $this->setContraintesCursus($contraintes);
        return $this;
    }

    public function setContraintesCursus($contraintes): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($contraintes as $c) {
            $this->addContrainteCursus($c);
        }
        return $this;
    }

    public function addContrainteCursus(ContrainteCursus $contrainte) : static
    {
        if ($this->hasContrainteCursus($contrainte)) return $this;
        $this->setContrainteCursusOption($contrainte, 'label', $contrainte->getAcronyme());
        $this->setContrainteCursusOption($contrainte, 'value', $contrainte->getId());
        return $this;
    }

    public function removeContrainteCursus($contrainte) : static
    {
        if (!$this->hasContrainteCursus($contrainte)) return $this;
        $options = $this->getOption('options');
        unset($options[$contrainte->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasContrainteCursus($contrainte) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($contrainte->getId(), $options));
    }

    public function getContrainteCursusOptions($contrainte) : array
    {
        if (!$this->hasContrainteCursus($contrainte)) return [];
        $options = $this->getOption('options');
        return $options[$contrainte->getId()];
    }

    public function getContrainteCursusAttributes($contrainte) : array
    {
        $options = $this->getContrainteCursusOptions($contrainte);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setContrainteCursusOption($contrainte, $key, $value) : static
    {
        $options = $this->getContrainteCursusOptions($contrainte);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$contrainte->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setContrainteCursusAttribute($contrainte, $key, $value) : static
    {
        $attributes = $this->getContrainteCursusAttributes($contrainte);
        $attributes[$key] = $value;
        $this->setContrainteCursusOption($contrainte, 'attributes', $attributes);
        return $this;
    }

}