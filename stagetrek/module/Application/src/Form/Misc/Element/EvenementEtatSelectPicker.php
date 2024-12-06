<?php


namespace Application\Form\Misc\Element;

use Application\Form\Abstrait\Element\AbstractSelectPicker;
use UnicaenEvenement\Entity\Db\Etat;

class EvenementEtatSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $etats = $this->getObjectManager()->getRepository(Etat::class)->findBy([],['id' => 'asc']);
        $this->setEtats($etats);
        return $this;
    }

    public function setEtats($etat): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($etat as $e){
            $this->addEtat($e);
        }
        return $this;
    }

    public function addEtat(Etat $etat) : static
    {
        if($this->hasEtat($etat)) return $this;
        $this->setEtatOption($etat, 'label', $etat->getLibelle());
        $this->setEtatOption($etat, 'value', $etat->getId());
        return $this;
    }
    public function removeEtat(Etat $etat) : static
    {
        if(!$this->hasEtat($etat)) return $this;
        $options = $this->getOption('options');
        unset($options[$etat->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasEtat(Etat $etat) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($etat->getId(), $options));
    }

    public function getEtatOptions(Etat $etat) : array
    {
        if(!$this->hasEtat($etat)) return [];
        $options = $this->getOption('options');
        return $options[$etat->getId()];
    }
    public function getEtatAttributes(Etat $etat) : array
    {
        $options = $this->getEtatOptions($etat);
        if(!key_exists('attributes', $options)){return [];}
        return $options['attributes'];
    }

    public function setEtatOption(Etat $etat, $key, $value) : static
    {
        $options = $this->getEtatOptions($etat);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$etat->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setEtatAttribute(Etat $etat, $key, $value) : static
    {
        $attributes = $this->getEtatAttributes($etat);
        $attributes[$key]=$value;
        $this->setEtatOption($etat, 'attributes', $attributes);
        return $this;
    }

}