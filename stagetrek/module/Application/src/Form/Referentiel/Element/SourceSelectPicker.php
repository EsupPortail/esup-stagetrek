<?php


namespace Application\Form\Referentiel\Element;

use Application\Entity\Db\Source;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class SourceSelectPicker extends AbstractSelectPicker
{
    public function setDefaultData() : static
    {
        $sources = $this->getObjectManager()->getRepository(Source::class)->findAll();
        usort($sources, function (Source $s1, Source $s2) {
            return $s1->getOrdre() - $s2->getOrdre();
        });
        $this->setSources($sources);
        return $this;
    }

    public function setSources($sources): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($sources as $s) {
            $this->addSource($s);
        }
        return $this;
    }

    public function addSource($source) : static
    {
        if ($this->hasSource($source)) return $this;
        $this->setSourceOption($source, 'label', $source->getLibelle());
        $this->setSourceOption($source, 'value', $source->getId());
        return $this;
    }

    public function removeSource($source) : static
    {
        if (!$this->hasSource($source)) return $this;
        $options = $this->getOption('options');
        unset($options[$source->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasSource($source) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($source->getId(), $options));
    }

    public function getSourceOptions($source) : array
    {
        if (!$this->hasSource($source)) return [];
        $options = $this->getOption('options');
        return $options[$source->getId()];
    }

    public function getSourceAttributes($source) : array
    {
        $options = $this->getSourceOptions($source);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setSourceOption($source, $key, $value) : static
    {
        $options = $this->getSourceOptions($source);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$source->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setSourceAttribute($source, $key, $value) : static
    {
        $attributes = $this->getSourceAttributes($source);
        $attributes[$key] = $value;
        $this->setSourceOption($source, 'attributes', $attributes);
        return $this;
    }

}