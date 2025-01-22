<?php

namespace Application\Form\Convention\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\DescriptionInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\StringLength;

class ModeleConventionStageFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use DescriptionInputAwareTrait;


    const CORPS = "corps";
    const CSS = "css";
    const HEADER = "header";
    const FOOTER = "footer";
    const TERRAINS = "terrainsStages";

    public function init(): void
    {
        $this->initIdInput();
        $this->setCodeLabel("Label du template");
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initDescriptionInput();
        $this->initCorpsInput();
        $this->initCssInput();
        $this->initTerrainsStagesInput();
    }

    private function initCorpsInput() : void
    {
        $this->add([
            'name' => self::CORPS,
            'type' => Textarea::class,
            'options' => [
                'label' => "Contenue de la convention",
            ],
            'attributes' => [
                'id' => self::CORPS,
                'placeholder' => 'Saisir le corps du modéle de convention',
                "rows" => 50,
                'class' => "macro-compatible"
            ],
        ]);

        $this->setInputfilterSpecification(self::CORPS, [
            "name" => self::CORPS,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                    ],
                ],
            ],
        ]);
    }

    private function initCssInput() : void
    {
        $this->add([
            'name' => self::CSS,
            'type' => Textarea::class,
            'options' => [
                'label' => "Régles de mise en page",
            ],
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StringTrim::class],
                ['name' => StripTags::class],
            ],
            'attributes' => [
                'id' => self::CSS,
                "placeholder" => "Saisir le css du modéle",
                "rows" => 5,
            ],
        ]);

        $this->setInputfilterSpecification(self::CSS,[
            "name" => self::CSS,
            'required' => false,
            'validators' => [[
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',//Garentie l'encodage
                        'min' => 0,
                    ],
            ]],
        ]);
    }

    private function initTerrainsStagesInput() : void
    {
        $this->add([
            'type' => TerrainStageSelectPicker::class,
            'name' => self::TERRAINS,
            'options' => [
                'label' => "Terrains de stages utilisant le modéle",
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::TERRAINS,
                'class' => 'selectpicker',
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' => "count > 3",
                'data-count-selected-text' => "{0} terrains",
                'title' => 'Sélectionner les terrains de stages',
            ],
        ]);

        $this->setInputfilterSpecification(self::TERRAINS, [
            "name" => self::TERRAINS,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }
}
