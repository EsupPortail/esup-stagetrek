<?php


namespace Application\Form\Contrainte\Element;

use Application\Entity\Db\ContrainteCursusPortee;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class ContrainteCursusPorteeSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $portees = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->findAll();
        $portees = ContrainteCursusPortee::sort($portees);
        $this->setContraintesCursusPortees($portees);
        return $this;
    }

    public function setContraintesCursusPortees($portees) : static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($portees as $p) {
            $this->addContrainteCursusPortee($p);
        }return $this;
    }

    public function addContrainteCursusPortee($portee) : static
    {
        if ($this->hasContrainteCursusPortee($portee)) return $this;
        $this->setContrainteCursusPorteeOption($portee, 'label', $portee->getLibelle());
        $this->setContrainteCursusPorteeOption($portee, 'value', $portee->getId());
        return $this;
    }

    public function removeContrainteCursusPortee($portee) : static
    {
        if (!$this->hasContrainteCursusPortee($portee)) return $this;
        $options = $this->getOption('options');
        unset($options[$portee->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasContrainteCursusPortee($portee): bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($portee->getId(), $options));
    }

    public function getContrainteCursusPorteeOptions($portee)
    {
        if (!$this->hasContrainteCursusPortee($portee)) return [];
        $options = $this->getOption('options');
        return $options[$portee->getId()];
    }

    public function getContrainteCursusPorteeAttributes($portee)
    {
        $options = $this->getContrainteCursusPorteeOptions($portee);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setContrainteCursusPorteeOption($portee, $key, $value)  : static
    {
        $options = $this->getContrainteCursusPorteeOptions($portee);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$portee->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setContrainteCursusPorteeAttribute($portee, $key, $value) : static
    {
        $attributes = $this->getContrainteCursusPorteeAttributes($portee);
        $attributes[$key] = $value;
        $this->setContrainteCursusPorteeOption($portee, 'attributes', $attributes);
        return $this;
    }

}