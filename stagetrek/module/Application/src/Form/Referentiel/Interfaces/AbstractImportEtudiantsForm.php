<?php

namespace Application\Form\Referentiel\Interfaces;


use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputFilterProviderInterface;

abstract class AbstractImportEtudiantsForm extends AbstractEntityForm implements ImportEtudiantsFormInterface,
    InputFilterProviderInterface
{
    public function init(): static
    {
        parent::init();
        $this->get(self::SUBMIT)->setLabel(self::LABEL_SUBMIT_IMPORT);
        $this->initFormKey();
        $this->initFormSource();
        return $this;
    }

    use InputFilterProviderTrait;


    public const INPUT_KEY = 'form_key';
    protected function initFormKey() : static
    {
        $this->add([
            "name" => self::INPUT_KEY,
            'type' => Hidden::class,
            'attributes' => [
                "id" => self::INPUT_KEY,
                "value" => $this->getKey(),
            ],
        ]);
        $this->setInputfilterSpecification(self::INPUT_KEY, [
            'name' => self::INPUT_KEY,
            'required' =>true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        return $this;
    }

    public const INPUT_SOURCE = 'form_source';
    protected function initFormSource() : static
    {
        $this->add([
            "name" => self::INPUT_SOURCE,
            'type' => Hidden::class,
            'attributes' => [
                "id" => self::INPUT_SOURCE,
                "value" => $this->getKey(), //Valeur par défaut
            ],
        ]);
        $this->setInputfilterSpecification(self::INPUT_SOURCE, [
            'name' => self::INPUT_SOURCE,
            'required' =>true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        return $this;
    }

    protected bool $actif = false;
    public function isActif(): bool
    {
        return $this->actif;
    }
    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }


}