<?php


namespace Application\Form\Contrainte\Fieldset;

use Application\Entity\Db\ContrainteCursusPortee;
use Application\Form\Contrainte\Element\ContrainteCursusPorteeSelectPicker;
use Application\Form\Contrainte\Validator\ContrainteCursusValidator;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\AcronymeInputAwareTrait;
use Application\Form\Misc\Traits\DescriptionInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Application\Form\TerrainStage\Element\CategorieStageSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Number;
use Laminas\I18n\Validator\IsInt;
use Laminas\Validator\Callback;

/**
 * Class ContrainteCursusFieldset
 * @package Application\Form\ContraintesCursus\Fieldset
 */
class ContrainteCursusFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    use LibelleInputAwareTrait;
    use AcronymeInputAwareTrait;
    use DescriptionInputAwareTrait;
    use OrdreInputAwareTrait;

    public function init(): void
    {
        $this->initIdInput();
        $this->initAcronymeInput();
        $this->initLibelleInput();
        $this->initDescriptionInput();
        $this->initOrdreInput();
        $this->initPorteeInput();
        $this->initCategorieInput();
        $this->initTerrainStageInput();
        $this->initBornesInput();
        $this->initDatesInput();
    }

    const PORTEE = "contrainteCursusPortee";
    protected function initPorteeInput() : void
    {
        $this->add([
            'type' => ContrainteCursusPorteeSelectPicker::class,
            'name' => self::PORTEE,
            'options' => [
                'label' => "Portée",
            ],
            'attributes' => [
                'id' => self::PORTEE,
            ],
        ]);
        /** @var ContrainteCursusPorteeSelectPicker $input */
        $input = $this->get(self::PORTEE);
        $portees = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->findAll();
        foreach ($portees as $portee) {
            $input->setContrainteCursusPorteeAttribute($portee, 'data-code', $portee->getCode());
        }



        $this->setInputfilterSpecification(self::PORTEE, [
            "name" => self::PORTEE,
            'required' => true,
            'validators' => [
                [
                    'name' => ContrainteCursusValidator::class,
                    'options' => [
                        'callback' => ContrainteCursusValidator::ASSERT_PORTEE,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }



    const CATEGORIE_STAGE = "categorieStage";
    protected function initCategorieInput() : void
    {
        $this->add([
            'type' => CategorieStageSelectPicker::class,
            'name' => self::CATEGORIE_STAGE,
            'options' => [
                'label' => "Catégorie",
                "empty_option" => "Sélectionner une catégorie",
            ],
            'attributes' => [
                'id' => self::CATEGORIE_STAGE,
                'autofocus' => true
            ],
        ]);

        $this->setInputfilterSpecification(self::CATEGORIE_STAGE, [
            "name" => self::CATEGORIE_STAGE,
            'required' => false,
        ]);
    }
    const TERRAIN_STAGE = "terrainStage";
    protected function initTerrainStageInput() : void
    {
        $this->add([
            'type' => TerrainStageSelectPicker::class,
            'name' => self::TERRAIN_STAGE,
            'options' => [
                'label' => "Terrain de stage",
                'empty_option' => "Sélectionner un terrain de stage",
            ],
            'attributes' => [
                'id' => self::TERRAIN_STAGE,
            ],
        ]);

        $this->setInputfilterSpecification(self::TERRAIN_STAGE, [
            "name" => self::TERRAIN_STAGE,
            'required' => false,
        ]);

    }
    const NB_STAGE_MIN = "nombreDeStageMin";
    const NB_STAGE_MAX = "nombreDeStageMax";
    protected function initBornesInput() : void
    {

        $this->add([
            'name' => self::NB_STAGE_MIN,
            'type' => Number::class,
            'options' => [
                'label' => "Nombre de stage(s) minimum à effectuer",
            ],
            'attributes' => [
                'id' => self::NB_STAGE_MIN,
                'step' => 'any',
                "min" => 0,
            ],
        ]);

        $this->add([
            'name' => self::NB_STAGE_MAX,
            'type' => Number::class,
            'options' => [
                'label' => "Nombre de stage(s) maximum à effectuer",
            ],
            'attributes' => [
                'id' => self::NB_STAGE_MAX,
                'step' => 'any',
                'min' => 0,
            ],
        ]);

        $this->setInputfilterSpecification(self::NB_STAGE_MIN, [
            "name" => self::NB_STAGE_MIN,
            'required' => false,
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
                [
                    'name' => ContrainteCursusValidator::class,
                    'options' => [
                        'callback' => ContrainteCursusValidator::ASSERT_NB_STAGE,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
        $this->setInputfilterSpecification(self::NB_STAGE_MAX, [
            "name" => self::NB_STAGE_MAX,
            'required' => false,
            'validators' => [
                [
                    'name' => IsInt::class,
                ],
                [
                    'name' => ContrainteCursusValidator::class,
                    'options' => [
                        'callback' => ContrainteCursusValidator::ASSERT_NB_STAGE,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    const DATE_DEBUT = "dateDebut";
    const DATE_FIN = "dateFin";
    protected function initDatesInput() : void
    {
        $this->add([
            'name' => self::DATE_DEBUT,
            'type' => Date::class,
            'options' => [
                'label' => "Date de début d'application",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT,
            ],
        ]);

        $this->add([
            'name' => self::DATE_FIN,
            'type' => Date::class,
            'options' => [
                'label' => "Date de fin d'application",
            ],
            'attributes' => [
                'id' => self::DATE_FIN,
            ],
        ]);
        $this->setInputfilterSpecification(self::DATE_DEBUT, [
            'name' => self::DATE_DEBUT,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "La date de début doit précédé la date de fin",
                    ],
                    'callback' => function ($value, $context = []) {
                        $date1 = $context[self::DATE_DEBUT];
                        $date2 = $context[self::DATE_FIN];
                        return $date1 < $date2;
                    }
                ],
            ]],
        ]);
        $this->setInputfilterSpecification(self::DATE_FIN, [
            'name' => self::DATE_FIN,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "La date de début doit précédé la date de fin",
                    ],
                    'callback' => function ($value, $context = []) {
                        $date1 = $context[self::DATE_DEBUT];
                        $date2 = $context[self::DATE_FIN];
                        return $date1 < $date2;
                    }
                ],
            ]],
        ]);
    }
}