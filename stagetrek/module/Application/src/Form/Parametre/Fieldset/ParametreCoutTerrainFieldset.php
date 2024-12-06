<?php


namespace Application\Form\Parametre\Fieldset;

use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Db\TerrainStage;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Number;
use Laminas\Validator\Callback;

/**
 * Class ParametreTerrainCoutAffectationFixeFieldset
 * @package Application\Form\Fieldset
 */
class ParametreCoutTerrainFieldset  extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    public function init(): void
    {
        $this->initIdInput();
        $this->initTerrainInput();
        $this->initCoutInput();
        $this->initUseCoutMedianInput();
    }

    const COUT = "cout";
    protected function initCoutInput(): static
    {
        $this->add([
            "name" => self::COUT,
            'type' => Number::class,
            'options' => [
                'label' => "Coût",
            ],
            'attributes' => [
                "id" => self::COUT,
                "min" => 0,
            ],
        ]);

        $this->setInputfilterSpecification(self::COUT, [
            'name' => self::COUT,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);
        return $this;
    }

    const TERRAIN_STAGE = "terrainStage";
    protected function initTerrainInput() : static
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
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Aucun terrain de stage selectionné.",
                        ],
                        'callback' => function ($value) {
                            return (isset($value) && $value!=0);
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Le terrain de stage selectionné n'a pas été trouvé.",
                        ],
                        'callback' => function ($value) {
                            $terrainId = $value;
                            if(!$terrainId||$terrainId=='') return false;
                            /** @var TerrainStage $terrain */
                            $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainId);
                            return (isset($terrain));

                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE =>"Le terrain de stage selectionné a déjà un cout fixe",
                        ],
                        'callback' => function ($value, $context = []) {
                            if(!key_exists(self::ID, $context)) return true; //Erreur mais pas du au fait que le lien existe
                            $currentId = $context[self::ID];
                            $terrainId = $value;
                            if(!$terrainId||$terrainId=='') return true; //False mais pas a cause de l'existance du parametre
                            /** @var ParametreTerrainCoutAffectationFixe $parametre */
                            $parametre =  $this->getObjectManager()->getRepository(ParametreTerrainCoutAffectationFixe::class)->findOneBy(['terrainStage' => $terrainId]);
                            if(!$parametre) return true;
                            if($currentId){
                                return $parametre->getId() == $currentId;
                            }
                            return false;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);

        return $this;
    }
    // Fonction qui permet de fixer le terrain de stage selectionné
    public function fixerTerrainStage(TerrainStage $terrain) : static
    {
        /** @var TerrainStageSelectPicker $input */
        $input = $this->get(self::TERRAIN_STAGE);
        $input->setTerrainsStages([$terrain]);
        $input->setEmptyOption(null);
        $input->setAttribute('data-live-search', false);
        return $this;
    }


    const COUT_MEDIAN = "use_cout_median";
    protected function initUseCoutMedianInput() : static
    {
        $this->add([
            'name' => self::COUT_MEDIAN,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Utiliser le cout médian",
            ],
            'attributes' => [
                'id' => self::COUT_MEDIAN,
            ],
        ]);

        $this->setInputfilterSpecification(self::COUT_MEDIAN,  [
            'name' => self::COUT_MEDIAN,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        return $this;
    }
}