<?php


namespace Application\Form\Referentiel\Element;

use Application\Entity\Db\ReferentielPromo;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class ReferentielPromoSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData(): static
    {
        $referentiel = $this->getObjectManager()->getRepository(ReferentielPromo::class)->findAll();
        usort($referentiel, function (ReferentielPromo $r1, ReferentielPromo $r2) {
            $s1 = $r1->getSource();
            $s2 = $r2->getSource();
            if($s1->getId() != $s2->getId()) {return $s1->getOrdre()-$s2->getOrdre();}
            return $r1->getOrdre()-$r2->getOrdre();
                 });
        $this->setReferentiels($referentiel);
        return $this;
    }

    public function setReferentiels($referentiels): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($referentiels as $referentiel) {
            $this->addReferentiel($referentiel);
        }
        return $this;
    }

    //Permet nottament de modifier le label de la catégorie
    public function addSource($source): static
    {
        if ($this->hasSource($source))
            return $this;
        $this->setSourceOption($source, 'label', $source->getLibelle());
        $this->setSourceOption($source, 'options', []);
        return $this;
    }

    public function addReferentiel($referentiel): static
    {
        if ($this->hasReferentiel($referentiel))
            return $this;
        $source = $referentiel->getSource();
        if (!$this->hasSource($source)) {
            $this->addSource($source);
        }
        $this->setReferentielOption($referentiel, 'label', $referentiel->getLibelle());
        $this->setReferentielOption($referentiel, 'value', $referentiel->getId());
        return $this;
    }

    public function removeSource($source): static
    {
        if (!$this->hasSource($source))
            return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$source->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function removeReferentiel($referentiel): static
    {
        if (!$this->hasReferentiel($referentiel))
            return $this;
        $source = $referentiel->getSource();
        $value_options = $this->getOption('value_options');
        unset($value_options[$source->getId()]['options'][$referentiel->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function hasSource($source): bool
    {
        $value_options = $this->getOption('value_options');
        return ($value_options && key_exists($source->getId(), $value_options));
    }

    public function hasReferentiel($referentiel): bool
    {
        $source = $referentiel->getSource();
        if (!$this->hasSource($source)) return false;
        $sourceOption = $this->getSourceOptions($source);
        return (key_exists('options', $sourceOption) && key_exists($referentiel->getId(), $sourceOption['options']));
    }

    public function getSourceOptions($source)
    {
        if (!$this->hasSource($source)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$source->getId()];
    }

    public function getSourceAttributes($source)
    {
        $sourceOptions = $this->getSourceOptions($source);
        if (!key_exists('attributes', $sourceOptions)) {
            return [];
        }
        return $sourceOptions['attributes'];
    }

    public function getReferentielOptions($referentiel)
    {
        if (!$this->hasReferentiel($referentiel)) return [];
        $source = $referentiel->getSource();
        $sourceOptions = $this->getSourceOptions($source);
        return $sourceOptions['options'][$referentiel->getId()];
    }

    public function getReferentielAttributes($referentiel)
    {
        $referentielOptions = $this->getReferentielOptions($referentiel);
        if (!key_exists('attributes', $referentielOptions)) {
            return [];
        }
        return $referentielOptions['attributes'];
    }

    public function setSourceOption($source, $key, $value): static
    {
        $options = $this->getSourceOptions($source);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$source->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setReferentielOption($referentiel, $key, $value): static
    {
        $options = $this->getReferentielOptions($referentiel);
        $options[$key] = $value;
        $source = $referentiel->getSource();
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$source->getId()]['options'][$referentiel->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    //Permet nottament de rajouter des data-attributes
    public function setSourceAttribute($source, $key, $value): static
    {
        $attributes = $this->getSourceAttributes($source);
        $attributes[$key] = $value;
        $this->setSourceOption($source, 'attributes', $attributes);
        return $this;
    }

    public function setReferentielAttribute($referentiel, $key, $value): static
    {
        $attributes = $this->getReferentielAttributes($referentiel);
        $attributes[$key] = $value;
        $this->setReferentielOption($referentiel, 'attributes', $attributes);
        return $this;
    }
}