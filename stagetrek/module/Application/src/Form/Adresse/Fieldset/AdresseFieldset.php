<?php


namespace Application\Form\Adresse\Fieldset;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Validator\StringLength;

/**
 * Class AdresseFieldset
 * @package Application\Form\Fieldset
 */
class AdresseFieldset extends Fieldset
{
    const FIELDSET_NAME = "adresse";

    /** Id/Name des inputs */
    const INPUT_ADRESSE = "adresse";
    const INPUT_COMPLEMENT = "complement";
    const INPUT_VILLE = "ville";
    const INPUT_VILLE_CODE = "villeCode";
    const INPUT_CP = "codePostal";
    const INPUT_CEDEX = "cedex";

    /** Libellés des inputs */
    const LABEL_ADRESSE = "Adresse";
    const LABEL_COMPLEMENT = "Complément";
    const LABEL_VILLE = "Ville";
    const LABEL_CP = "Code Postal";
    const LABEL_CEDEX = "Cedex";

    /** Placeholder des inputs */
    const PLACEHOLDER_ADRESSE = "Saisir une adresse";
    const PLACEHOLDER_COMPLEMENT = "Complément de l'adresse";
    const PLACEHOLDER_VILLE = "Saisir une ville";
    const PLACEHOLDER_CP = "Saisir un code postal";
    const PLACEHOLDER_CEDEX = "Cedex";

    //Options pour les filtres sur les choix
    public function init(): void
    {
        $this->add([
            "name" => self::INPUT_ADRESSE,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_ADRESSE,
            ],
            'attributes' => [
                "id" => self::INPUT_ADRESSE,
                "placeholder" => self::PLACEHOLDER_ADRESSE,
            ],
        ]);
        $this->add([
            "name" => self::INPUT_COMPLEMENT,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_COMPLEMENT,
            ],
            'attributes' => [
                "id" => self::INPUT_COMPLEMENT,
                "placeholder" => self::PLACEHOLDER_COMPLEMENT,
            ],
        ]);

        $this->add([
            "name" => self::INPUT_VILLE,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_VILLE,
            ],
            'attributes' => [
                "id" => self::INPUT_VILLE,
                "placeholder" => self::PLACEHOLDER_VILLE,
                'class' => 'individu-finder',
            ],
        ]);

        $this->add([
            "name" => self::INPUT_VILLE_CODE,
            'type' => Hidden::class,
            'attributes' => [
                "id" => self::INPUT_VILLE_CODE,
            ],
        ]);

        $this->add([
            "name" => self::INPUT_CP,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_CP,
            ],
            'attributes' => [
                "id" => self::INPUT_CP,
                "placeholder" => self::PLACEHOLDER_CP,
            ],
        ]);
        $this->add([
            "name" => self::INPUT_CEDEX,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_CEDEX,
            ],
            'attributes' => [
                "id" => self::INPUT_CEDEX,
                "placeholder" => self::PLACEHOLDER_CEDEX,
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            self::INPUT_ADRESSE => [
                "name" => self::INPUT_ADRESSE,
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            self::INPUT_COMPLEMENT => [
                "name" => self::INPUT_COMPLEMENT,
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            self::INPUT_VILLE => [
                "name" => self::INPUT_VILLE,
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
                            'max' => 255,
                        ],
                    ],
//                    [ Pas de vérification sur l'existance de la ville afin de permettre des champs libre
//                        'name' => Callback::class,
//                        'options' => [
//                            'messages' => [
//                                Callback::INVALID_VALUE => "Vous devez rechercher puis sélectionner une ville dans la liste proposée.",
//                            ],
//                            'callback' => function ($value, $context) {
//                                if(!isset($value) || $value == ""){//Pas de ville
//                                    return true;
//                                }
//                                //Si la ville a été recherché, le code doit être différent de 0
//                                return (isset($context[self::INPUT_VILLE_CODE]) && $context[self::INPUT_VILLE_CODE]!="0");
//                            },
//                            'break_chain_on_failure' => true,
//                        ],
//                    ],
                ],
            ],
            self::INPUT_VILLE_CODE => [
                "name" => self::INPUT_VILLE_CODE,
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ]
            ],
            self::INPUT_CP => [
                "name" => self::INPUT_CP,
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
                            'min' => 0,//Pour permettre null
                            'max' => 6,
                        ],
                    ],
                ],
            ],
            self::INPUT_CEDEX => [
                "name" => self::INPUT_CEDEX,
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
                            'min' => 0,//Pour permettre null
                            'max' => 25,
                        ],
                    ],
                ],
            ],
        ];
    }
}