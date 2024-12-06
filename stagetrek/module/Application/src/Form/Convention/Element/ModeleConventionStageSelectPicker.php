<?php

namespace Application\Form\Convention\Element;

use Application\Entity\Db\ModeleConventionStage;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class ModeleConventionStageSelectPicker extends AbstractSelectPicker
{

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function setDefaultData() : static
    {
        $modeles = $this->getObjectManager()->getRepository(ModeleConventionStage::class)
            ->findBy([], ['libelle' => 'ASC']);
        $this->setModeleConventionsStages($modeles);
        return $this;
    }

    public function setModeleConventionsStages(array $modeles): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($modeles as $m) {
            $this->addModeleConventionStage($m);
        }
        return $this;
    }

    public function addModeleConventionStage(ModeleConventionStage $modele) : static
    {
        if ($this->hasModeleConventionStage($modele)) return $this;
        $this->setModeleConventionStageOption($modele, 'label', $modele->getLibelle());
        $this->setModeleConventionStageOption($modele, 'value', $modele->getId());
        return $this;
    }

    public function removeModeleConventionStage(ModeleConventionStage $modele) : static
    {
        if (!$this->hasModeleConventionStage($modele)) return $this;
        $options = $this->getOption('options');
        unset($options[$modele->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasModeleConventionStage(ModeleConventionStage $modele) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($modele->getId(), $options));
    }

    public function getModeleConventionStageOptions(ModeleConventionStage $modele) : array
    {
        if (!$this->hasModeleConventionStage($modele)) return [];
        $options = $this->getOption('options');
        return $options[$modele->getId()];
    }

    public function getModeleConventionStageAttributes(ModeleConventionStage $modele) : array
    {
        $options = $this->getModeleConventionStageOptions($modele);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setModeleConventionStageOption(ModeleConventionStage $modele, string $key, mixed $value) : static
    {
        $options = $this->getModeleConventionStageOptions($modele);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$modele->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }


    public function setModeleConventionStageOptions(ModeleConventionStage $modele, string $key, mixed $value) : static
    {
        $options = $this->getModeleConventionStageOptions($modele);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$modele->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setModeleConventionStageAttributes(ModeleConventionStage $modele, $key, $value): static
    {
        $attributes = $this->getModeleConventionStageAttributes($modele);
        $attributes[$key] = $value;
        $this->setModeleConventionStageOptions($modele, 'attributes', $attributes);
        return $this;
    }

}
