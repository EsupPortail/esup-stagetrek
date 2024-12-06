<?php


namespace Application\Form\Misc\Element;

use Application\Form\Abstrait\Element\AbstractSelectPicker;
use UnicaenEvenement\Entity\Db\Type;

class EvenementTypeSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $types = $this->getObjectManager()->getRepository(Type::class)->findBy([],['libelle' => 'asc']);
        $this->setTypes($types);
        return $this;
    }

    public function setTypes($type): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($type as $t){
            $this->addType($t);
        }
        return $this;
    }

    public function addType(Type $type) : static
    {
        if($this->hasType($type)) return $this;
        $this->setTypeOption($type, 'label', $type->getLibelle());
        $this->setTypeOption($type, 'value', $type->getId());
        return $this;
    }
    public function removeType(Type $type) : static
    {
        if(!$this->hasType($type)) return $this;
        $options = $this->getOption('options');
        unset($options[$type->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasType(Type $type) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($type->getId(), $options));
    }

    public function getTypeOptions(Type $type) : array
    {
        if(!$this->hasType($type)) return [];
        $options = $this->getOption('options');
        return $options[$type->getId()];
    }
    public function getTypeAttributes(Type $type) : array
    {
        $options = $this->getTypeOptions($type);
        if(!key_exists('attributes', $options)){return [];}
        return $options['attributes'];
    }

    public function setTypeOption(Type $type, $key, $value) :static
    {
        $options = $this->getTypeOptions($type);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$type->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setTypeAttribute(Type $type, $key, $value): static
    {
        $attributes = $this->getTypeAttributes($type);
        $attributes[$key]=$value;
        $this->setTypeOption($type, 'attributes', $attributes);
        return $this;
    }

}