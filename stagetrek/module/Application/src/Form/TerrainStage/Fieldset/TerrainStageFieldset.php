<?php


namespace Application\Form\TerrainStage\Fieldset;

use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Convention\Element\ModeleConventionStageSelectPicker;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Application\Form\TerrainStage\Element\CategorieStageSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStagePrincipalSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSecondaireSelectPicker;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class TerrainStageFieldset
 * @package Application\Form\TerrainStage\Fieldset
 */
class TerrainStageFieldset extends AbstractEntityFieldset
    implements HasTagInputInterface
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use TagInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initCategorieInput();
        $this->initPlacesInput();
        $this->initAdresseInput();
        $this->initInfosInput();
        $this->initContraintesInput();
        $this->initTerrainLinkerInput();
        $this->initModeleConventionInput();
        $this->initTagsInputs();
        return $this;
    }


    const CATEGORIE_STAGE = "categorieStage";
    private function initCategorieInput() : void
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
           'required' => true,
           'filters' => [
               ['name' => ToNull::class],
               ['name' => ToInt::class],
           ]
       ]);
    }

    const MIN_PLACE = "minPlace";
    const IDEAL_PLACE = "idealPlace";
    const MAX_PLACE = "maxPlace";

    private function initPlacesInput() : void
    {
        $this->add([
            "name" => self::MIN_PLACE,
            "type" => Number::class,
            "options" => [
                "label" => "Nombre minimum de place",
            ],
            "attributes" => [
                "id" => self::MIN_PLACE,
                "min" => 0,
            ],
        ]);

        $this->add([
            "name" => self::IDEAL_PLACE,
            "type" => Number::class,
            "options" => [
                "label" => "Nombre idéal de place",
            ],
            "attributes" => [
                "id" => self::IDEAL_PLACE,
                "min" => 0,
            ],
        ]);

        $this->add([
            "name" => self::MAX_PLACE,
            "type" => Number::class,
            "options" => [
                "label" => "Nombre maximum de place",
            ],
            "attributes" => [
                "id" => self::MAX_PLACE,
                "min" => 0,
            ],
        ]);

        $minVsIdeal = [
            'name' => Callback::class,
            'options' => [
                'messages' => [
                    Callback::INVALID_VALUE => "Le nombre idéal de place doit être inférieur ou égal  au nombre maximum de place",
                ],
                'callback' => function ($value, $context = []) {
                    return intval($context[self::IDEAL_PLACE]) <= intval($context[self::MAX_PLACE]);
                },
                'break_chain_on_failure' => false,
            ],
        ];
        $idealVsMax = [
            'name' => Callback::class,
            'options' => [
                'messages' => [
                    Callback::INVALID_VALUE => "Le nombre minimum de place doit être inférieur ou égal au nombre idéal de place",
                ],
                'callback' => function ($value, $context = []) {
                    return intval($context[self::MIN_PLACE]) <= intval($context[self::IDEAL_PLACE]);
                },
                'break_chain_on_failure' => false,
            ],
        ];

        $this->setInputfilterSpecification(self::MIN_PLACE, [
            'name' => self::MIN_PLACE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [$minVsIdeal],
        ]);

        $this->setInputfilterSpecification(self::IDEAL_PLACE, [
            'name' => self::IDEAL_PLACE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' =>  [$minVsIdeal, $idealVsMax]
        ]);

        $this->setInputfilterSpecification(self::MAX_PLACE, [
            'name' => self::MAX_PLACE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' =>  [
                $idealVsMax
            ]
        ]);
    }

    const ADRESSE = "adresse";
    private function initAdresseInput(): void
    {
        $this->add([
            "name" => self::ADRESSE,
            'type' => AdresseFieldset::class,
            'attributes' => [
                "id" => self::ADRESSE
            ],
        ]);

        $this->setInputfilterSpecification(self::ADRESSE, [
            "name" => self::ADRESSE,
            'required' => true,
        ]);

    }

    const SERVICE = "service";
    const INFOS = "infos";
    const LIEN = "lien";
    const HORS_SUBDIVISION = "horsSubdivision";
    const ACTIF = "actif";
    private function initInfosInput(): void
    {

        $this->add([
            "name" => self::SERVICE,
            'type' => Text::class,
            'options' => [
                'label' => "Service"
            ],
            'attributes' => [
                "id" => self::SERVICE,
                "placeholder" => "Saisir le nom du service",
            ],
        ]);

        $this->add([
            "name" => self::INFOS,
            'type' => TextArea::class,
            'options' => [
                'label' => "Informations complémentaires",
            ],
            'attributes' => [
                "id" => self::INFOS,
                "placeholder" => "Saisir des informations complémentaires",
                "rows" => 5,
            ],
        ]);

        $this->add([
            "name" => self::LIEN,
            'type' => Text::class,
            'options' => [
                'label' => "Liens complémentaires",
            ],
            'attributes' => [
                "id" => self::LIEN,
                "placeholder" => "URL d'un lien pour plus d'informations sur le terrain de stage",
            ],
        ]);

        $this->add([
            'name' => self::HORS_SUBDIVISION,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Terrain de stage Hors Subdivision",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::HORS_SUBDIVISION,
                'value' => 0,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::ACTIF,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Actif",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::ACTIF,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);


        $this->setInputfilterSpecification(self::SERVICE, [
            "name" => self::SERVICE,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::INFOS, [
            "name" => self::INFOS,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class,
                    'options' => [
                        'allowTags' => [
                            'p', 'strong', 'em', 'abbr', 'ul', 'li'
                        ],
                        'allowAttribs' => [
                            'title',
                        ]
                    ],
                ],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 0
                    ],
                ],
            ],
        ]);

        $this->setInputfilterSpecification(self::LIEN, [
            "name" => self::LIEN,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $this->setInputfilterSpecification(self::HORS_SUBDIVISION, [
            "name" => self::HORS_SUBDIVISION,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::ACTIF, [
            "name" => self::ACTIF,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const PREFERENCES_AUTORISEES = "preferencesAutorisees";
    const RESTRICTIONS_TERRAIN_NIVEAU_ETUDE = "niveauxEtudesContraints";
    private function initContraintesInput() : void
    {
        $this->add([
            'name' => self::PREFERENCES_AUTORISEES,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Ouvert à la définition des préférences",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::PREFERENCES_AUTORISEES,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE,
            'type' => NiveauEtudeSelectPicker::class,
            'options' => [
                'label' => "Restreindre le terrain de stage pour les étudiants de :",
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE,
                'class' => 'selectpicker',
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-live-search' => false,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' => "count > 3",
                'data-count-selected-text' => "{0} niveaux selectionnés",
                'title' => "Sélectionner un niveau d'étude",
            ],
        ]);

        $this->setInputfilterSpecification(self::PREFERENCES_AUTORISEES, [
            'name' => self::PREFERENCES_AUTORISEES,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE, [
            'name' => self::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const TERRAINS_PRINCIPAUX_ASSOCIES = "terrainsPrincipaux";
    const TERRAINS_SECONDAIRES_ASSOCIES = "terrainsSecondaires";
    private function initTerrainLinkerInput() : void
    {
        $this->add([
            'name' => self::TERRAINS_PRINCIPAUX_ASSOCIES,
            'type' => TerrainStagePrincipalSelectPicker::class,
            'options' => [
                'label' => "Terrains de stages principaux associés",
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::TERRAINS_PRINCIPAUX_ASSOCIES,
                'class' => 'selectpicker',
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' => "count > 3",
                'data-count-selected-text' => "{0} terrains associés",
                'title' => "Aucun terrain",
            ],
        ]);

        $this->add([
            'name' => self::TERRAINS_SECONDAIRES_ASSOCIES,
            'type' => TerrainStageSecondaireSelectPicker::class,
            'options' => [
                'label' => "Terrains de stages secondaires associés",
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::TERRAINS_SECONDAIRES_ASSOCIES,
                'class' => 'selectpicker',
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' => "count > 3",
                'data-count-selected-text' => "{0} terrains associés",
                'title' => "Aucun terrain"
            ],
        ]);


        $this->setInputfilterSpecification(self::TERRAINS_PRINCIPAUX_ASSOCIES, [
            "name" => self::TERRAINS_PRINCIPAUX_ASSOCIES,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::TERRAINS_SECONDAIRES_ASSOCIES, [
            "name" => self::TERRAINS_SECONDAIRES_ASSOCIES,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const MODELE_CONVENTION = "modeleConventionStage";
    private function initModeleConventionInput() : void
    {
        $this->add([
            'name' => self::MODELE_CONVENTION,
            'type' => ModeleConventionStageSelectPicker::class,
            'options' => [
                'label' => "Modéle de convention de stage",
                "empty_option" => "Sélectionner un modéle de convention",
            ],
            'attributes' => [
                'id' => self::MODELE_CONVENTION,
                'autofocus' => true,
                'class' => 'selectpicker',
                'data-live-search' => false,
            ],
        ]);

        $this->setInputfilterSpecification(self::MODELE_CONVENTION, [
            "name" => self::MODELE_CONVENTION,
            'required' => false,
        ]);
    }

    public function getTagsAvailables(): array
    {
        $tags = $this->getTagService()->getTags();
        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if ($c1->getId() !== $c2->getId()) {
                //Trie spécifique : on met d'abord la catégorie Années
                if ($c1->getCode() == CategorieTagProvider::TERRAIN
                    || $c1->getCode() == CategorieTagProvider::CATEGORIE_STAGE
                ) {
                    return -1;
                }
                if ($c2->getCode() == CategorieTagProvider::TERRAIN
                    || $c2->getCode() == CategorieTagProvider::CATEGORIE_STAGE
                ) {
                    return 1;
                }
                if ($c1->getOrdre() < $c2->getOrdre()) return -1;
                if ($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if ($t1->getOrdre() < $t2->getOrdre()) return -1;
            if ($t2->getOrdre() < $t1->getOrdre()) return 1;
            return ($t1->getId() < $t2->getId()) ? -1 : 1;
        });
        return $tags;
    }
}