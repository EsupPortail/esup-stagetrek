<?php


namespace Application\Form\Annees\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Date;
use Laminas\Validator\Callback;

/**
 * Class AnneeUniversitaireFieldset
 * @package Application\Form\Annees\Fieldset
 */
class AnneeUniversitaireFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    use LibelleInputAwareTrait;

    const DATE_DEBUT = "dateDebut";
    const DATE_FIN = "dateFin";

    public function init(): void
    {
        $this->initIdInput();
        $this->initLibelleInput();
        $this->initDatesInputs();
    }

    protected function initDatesInputs() : static
    {
        $this->add([
            'name' => self::DATE_DEBUT,
            'type' => Date::class,
            'options' => [
                'label' => "Date de debut",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT,
            ],
        ]);

        $this->add([
            'name' => self::DATE_FIN,
            'type' => Date::class,
            'options' => [
                'label' => "Date de fin",
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
                'validators' => [
                    [
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
                    ]
                ]
        ]);

        $this->setInputfilterSpecification(self::DATE_FIN, [
                'name' => self::DATE_FIN,
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
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
                    ],
                ],
        ]);
        return $this;
    }
}