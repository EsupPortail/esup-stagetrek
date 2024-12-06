<?php


namespace Application\Form\Misc\Element;

use Application\Form\Abstrait\Element\AbstractSelectPicker;
use UnicaenEtat\Entity\Db\EtatType;

class EtatTypeSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $etatsTypes = $this->getObjectManager()->getRepository(EtatType::class)->findAll();
        usort($etatsTypes, function (EtatType $e1, EtatType $e2) {
            $c1 = $e1->getCategorie();
            $c2 = $e2->getCategorie();
            if($c1->getId() !== $c2->getId()){
                if($c1->getOrdre() < $c2->getOrdre()) return -1;
                if($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if($e1->getOrdre() < $e2->getOrdre()) return -1;
            if($e2->getOrdre() < $e1->getOrdre()) return 1;
            return ($e1->getId() < $e2->getId()) ? -1 : 1;
        });
        $this->setEtatsTypes($etatsTypes);
        return $this;
    }

    public function setEtatsTypes(array $etatsTypes) : static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($etatsTypes as $e) {
            $this->addEtatType($e);
        }
        return $this;
    }

    public function addEtatType(EtatType $etatType) : static
    {
        if ($this->hasEtatType($etatType)) return $this;
        $this->setEtatTypeOption($etatType, 'label', $etatType->getLibelle());
        $this->setEtatTypeOption($etatType, 'value', $etatType->getId());
        return $this;
    }

    public function removeEtatType(EtatType $etatType) : static
    {
        if (!$this->hasEtatType($etatType)) return $this;
        $options = $this->getOption('options');
        unset($options[$etatType->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasEtatType(EtatType $etatType) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($etatType->getId(), $options));
    }

    public function getEtatTypeOptions(EtatType $etatType) : array
    {
        if (!$this->hasEtatType($etatType)) return [];
        $options = $this->getOption('options');
        return $options[$etatType->getId()];
    }

    public function getEtatTypeAttributes(EtatType $etatType)
    {
        $options = $this->getEtatTypeOptions($etatType);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setEtatTypeOption(EtatType $etatType, string $key, mixed $value) : static
    {
        $options = $this->getEtatTypeOptions($etatType);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$etatType->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setEtatTypeAttribute(EtatType $etatType, string $key, mixed $value) : static
    {
        $attributes = $this->getEtatTypeAttributes($etatType);
        $attributes[$key] = $value;
        $this->setEtatTypeOption($etatType, 'attributes', $attributes);
        return $this;
    }

}