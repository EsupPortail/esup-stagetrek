<?php


namespace Application\Form\Notification\Fieldset;

use Application\Form\Abstrait\Traits\FormElementTrait;
use Application\Form\Misc\Element\RoleSelectPicker;
use Application\Provider\Notification\MessageInfoProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;

/**
 * Class MessageInfoFieldset
 * @package Application\Form\MessageFieldset
 */
class MessageInfoFieldset extends Fieldset
    implements
    InputFilterProviderInterface
{
    use FormElementTrait;

    /** Id/Name des inputs */
    const INPUT_TITLE = "title";
    const INPUT_MESSAGE = "message";
    const INPUT_PRIORITY = "priority";
    const INPUT_DATE = "dateMessage";
    const INPUT_ACTIF = "actif";
    const INPUT_ROLES = "roles";



    /** Libellés des inputs */
    const LABEL_TITLE = "Titre";
    const LABEL_MESSAGE = "Message";
    const LABEL_PRIORITY = "Priorité";
    const LABEL_DATE = "Date du message";
    const LABEL_ACTIF = "Message actif ?";
    const LABEL_ROLES = "Uniquement visible pour les roles";

    /** Placeholder des inputs */
    const EMPTY_OPTION_PRIORITY = "Sélectionner une priorité";
    const PLACEHOLDER_TITLE = "Saisir un titre";
    const PLACEHOLDER_MESSAGE = "Saisir un message";

    public function init(): void
    {
        $this->add([
            "name" => self::INPUT_TITLE,
            'type' => Text::class,
            'options' => [
                'label' => self::LABEL_TITLE,
            ],
            'attributes' => [
                "id" => self::INPUT_TITLE,
                "placeholder" => self::PLACEHOLDER_TITLE,
            ],
        ]);
        $this->add([
            "name" => self::INPUT_MESSAGE,
            'type' => Textarea::class,
            'options' => [
                'label' => self::LABEL_MESSAGE,
            ],
            'attributes' => [
                "id" => self::INPUT_MESSAGE,
                "placeholder" => self::PLACEHOLDER_MESSAGE,
                "rows" => 5,
                'class' => "no-resize",
            ],
        ]);

        $this->add([
            'type' => Select::class,
            'name' => self::INPUT_PRIORITY,
            'options' => [
                'label' => self::LABEL_PRIORITY,
                'value_options' => [
                    MessageInfoProvider::INFO => [
                        'label' => "Information",
                        'value' =>  MessageInfoProvider::INFO,
                        'attributes' => [
                            'data-icon' => "fas fa-exclamation-circle text-primary",
                        ]
                    ],
                    MessageInfoProvider::SUCCESS => [
                        'label' => "Succés",
                        'value' =>  MessageInfoProvider::SUCCESS,
                        'attributes' => [
                            'data-icon' => "fas fa-check-circle text-success",
                        ]
                    ],
                    MessageInfoProvider::WARNING => [
                        'label' => "Attention",
                        'value' =>  MessageInfoProvider::WARNING,
                        'attributes' => [
                            'data-icon' => "fas fa-exclamation-triangle text-warning",
                        ]
                    ],
                    MessageInfoProvider::ERROR => [
                        'label' => "Erreur",
                        'value' =>  MessageInfoProvider::ERROR,
                        'attributes' => [
                            'data-icon' => "fas fa-exclamation-triangle text-danger",
                        ]
                    ],
                ],
                'empty_option' => self::EMPTY_OPTION_PRIORITY,
            ],
            'attributes' => [
                'class' => "selectpicker",
                'autofocus' => true,
                'data-tick-icon' => 'fa fa-check text-success',
                'id' => self::INPUT_PRIORITY,
            ],
        ]);


        $this->add([
            'name' => self::INPUT_DATE,
            'type' => Date::class,
            'options' => [
                'label' => self::LABEL_DATE,
            ],
            'attributes' => [
                'id' => self::INPUT_DATE,
            ],
        ]);


        $this->add([
            'name' => self::INPUT_ACTIF,
            'type' => Checkbox::class,
            'options' => [
                'label' => self::LABEL_ACTIF,
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_ACTIF,
                'value' => 1,
                'class' => 'form-check-input'
            ],
        ]);

        $this->add([
            'type' => RoleSelectPicker::class,
            'name' => self::INPUT_ROLES,
            'options' => [
                'label' => self::LABEL_ROLES
            ],
            'attributes' => [
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' =>'count > 3',
                'data-count-selected-text' => '{0} roles selectionnés',
                'data-actions-box'=>true,
                'data-select-all-text'=>"Tout les rôles 
                    <span class='text-small text-muted'>(Utilisateurs connectées)</span>",
                'data-deselect-all-text'=>"Aucun rôles
                    <span class='text-small text-muted'>(Message publique)</span>",
                'title'=> 'Message publique',
                'data-live-search'=>false,
                'id' => self::INPUT_ROLES,
            ],
        ]);

    }

    public function getInputFilterSpecification(): array
    {
        return [
            self::INPUT_TITLE => [
                "name" => self::INPUT_TITLE,
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [[
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ]],
            ],

            self::INPUT_MESSAGE => [
                "name" => self::INPUT_TITLE,
                'required' => true,
                'filters' => [
//                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [[
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                    ],
                ]],
            ],

            self::INPUT_PRIORITY => [
                "name" => self::INPUT_PRIORITY,
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_DATE => [
                "name" => self::INPUT_DATE,
                'required' => true,
            ],
            self::INPUT_ACTIF => [
                "name" => self::INPUT_ACTIF,
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],

            self::INPUT_ROLES => [
                "name" => self::INPUT_ROLES,
                'required' => false,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}