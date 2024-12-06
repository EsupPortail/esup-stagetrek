<?php


namespace Application\Form\Parametre\Element;

use Application\Entity\Db\NiveauEtude;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class NiveauEtudeSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData(): static
    {
        $niveaux = $this->getObjectManager()->getRepository(NiveauEtude::class)->findAll();
        usort($niveaux, function (NiveauEtude $n1, NiveauEtude $n2) {
            if ($n1->getOrdre() - $n2->getOrdre() != 0) {
                return $n1->getOrdre() - $n2->getOrdre();
            }
            return strcmp($n1->getLibelle(), $n2->getLibelle());
        });
        $this->setNiveauxEtudes($niveaux);
        return $this;
    }

    public function setNiveauxEtudes($niveaux): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($niveaux as $n){
            $this->addNiveauEtude($n);
        }
        return $this;
    }

    public function addNiveauEtude($niveau): static
    {
        if($this->hasNiveauEtude($niveau))
            return $this;
        $this->setNiveauEtudeOption($niveau, 'label', $niveau->getLibelle());
        $this->setNiveauEtudeOption($niveau, 'value', $niveau->getId());
        return $this;
    }
    public function removeNiveauEtude($niveau): static
    {
        if(!$this->hasNiveauEtude($niveau))
            return $this;
        $options = $this->getOption('options');
        unset($options[$niveau->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasNiveauEtude($niveau): bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($niveau->getId(), $options));
    }

    public function getNiveauEtudeOptions($niveau){
        if(!$this->hasNiveauEtude($niveau)) return [];
        $options = $this->getOption('options');
        return $options[$niveau->getId()];
    }
    public function getNiveauEtudeAttributes($niveau){
        $options = $this->getNiveauEtudeOptions($niveau);
        if(!key_exists('attributes', $options)){return [];}
        return $options['attributes'];
    }

    public function setNiveauEtudeOption($niveau, $key, $value): static
    {
        $options = $this->getNiveauEtudeOptions($niveau);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$niveau->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setNiveauEtudeAttribute($niveau, $key, $value): static
    {
        $attributes = $this->getNiveauEtudeAttributes($niveau);
        $attributes[$key]=$value;
        $this->setNiveauEtudeOption($niveau, 'attributes', $attributes);
        return $this;
    }

}