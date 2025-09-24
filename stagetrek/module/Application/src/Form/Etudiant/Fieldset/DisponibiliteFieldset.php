<?php


namespace Application\Form\Etudiant\Fieldset;

use Application\Form\Etudiant\Validator\DisponibiliteValidator;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Laminas\Filter\DateTimeSelect;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;

/**
 * Class DisponibiliteFieldset
 * @package Application\Form\Fieldset
 */
class DisponibiliteFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;

    /** Id/Name des inputs */
    const ID = "id";
    const ETUDIANT = "etudiant";
    const DATE_DEBUT = "dateDebut";
    const DATE_FIN = "dateFin";
    const INFO = "informationComplementaire";
    const FORCER_DISPONIBILITE = "forcerDisponibilite";

    public function init() : static
    {
        $this->initIdInput();
        $this->initEtudiantInput();
        $this->initDatesInputs();
        $this->initPropertiesInputs();
        return $this;
    }
    private function initEtudiantInput(): void
    {
        $this->add([
            "name" => self::ETUDIANT,
            'type' => Hidden::class,
            'attributes' => [
                "id" => self::ETUDIANT,
            ],
        ]);
        $this->setInputfilterSpecification(self::ETUDIANT, [
            'name' => self::ETUDIANT,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => DisponibiliteValidator::class,
                    'options' => [
                        'callback' => DisponibiliteValidator::ASSERT_ETUDIANT,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    private function initDatesInputs(): void
    {
        $this->add([
            'name' => self::DATE_DEBUT,
            'type' => Date::class,
            'options' => [
                'label' => "Début le",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT,
            ],
        ]);
        $this->add([
            'type' => Date::class,
            'name' => self::DATE_FIN,
            'options' => [
                'label' => "Fin le",
            ],
            'attributes' => [
                'id' => self::DATE_FIN,
            ],
        ]);

        $this->setInputfilterSpecification(self::DATE_DEBUT, [
            'name' => self::DATE_DEBUT,
            'required' => true,
            'filters' => [
                ['name' => DateTimeSelect::class],
            ],
            'validators' => [
                [
                    'name' => DisponibiliteValidator::class,
                    'options' => [
                        'callback' => DisponibiliteValidator::ASSERT_DATE_DEBUT,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);

        $this->setInputfilterSpecification(self::DATE_FIN, [
            'name' => self::DATE_FIN,
            'required' => true,
            'filters' => [
                ['name' => DateTimeSelect::class],
            ],
            'validators' => [
                [
                    'name' => DisponibiliteValidator::class,
                    'options' => [
                        'callback' => DisponibiliteValidator::ASSERT_DATE_FIN,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    private function initPropertiesInputs() : void
    {
        $this->add([
            'name' => self::INFO,
            'type' => Text::class,
            'options' => [
                'label' => "Informations complémentaires"
            ],
            'attributes' => [
                'id' => self::INFO,
                'placeholder' => "Informations complémentaires",
            ],
        ]);

        $this->add([
            'name' => self::FORCER_DISPONIBILITE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Forcer la disponibilité",
            ],
            'attributes' => [
                'id' => self::FORCER_DISPONIBILITE,
            ],
        ]);

        $this->setInputfilterSpecification(self::INFO, [
            'name' => self::INFO,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        $this->setInputfilterSpecification(self::FORCER_DISPONIBILITE, [
            'name' => self::FORCER_DISPONIBILITE,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }
}