<?php

namespace Application\Form\Groupe;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireEtatSelectPicker;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;

/**
 * Class GroupeRechercheForm
 * @package Application\Form\Etudiant
 */
class GroupeRechercheForm extends AbstractRechercheForm
{
    const INPUT_LIBELLE = "libelle";
    const INPUT_ANNEE = "annee_universitaire";
    const INPUT_NIVEAU = "niveau_etude";
    const INPUT_ETAT ="annee_etat";

    public function init(): static
    {
        parent::init();

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_LIBELLE,
            'options' => [
                'label' => "Libellé",
            ],
            'attributes' => [
                'id' => self::INPUT_LIBELLE,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'type' => AnneeUniversitaireSelectPicker::class,
            'name' => self::INPUT_ANNEE,
            'options' => [
                'label' => 'Année universitaire',
            ],
            'attributes' => [
                'id' => self::INPUT_ANNEE,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner une année",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} années selectionnées",
            ],
        ]);

        $this->add([
            'type' => NiveauEtudeSelectPicker::class,
            'name' => self::INPUT_NIVEAU,
            'options' => [
                'label' => "Niveau d'étude",
            ],
            'attributes' => [
                'id' => self::INPUT_NIVEAU,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner un niveau d'étude",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} niveaux selectionnés",
            ],
        ]);
        $this->add([
            'type' => AnneeUniversitaireEtatSelectPicker::class,
            'name' => self::INPUT_ETAT,
            'options' => [
                'label' => "État de l'année",
//                'empty_option' => 'Sélectionner un état',
            ],
            'attributes' => [
                'id' => self::INPUT_ETAT,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner un état",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} etats selectionnés",
            ],
        ]);
        return $this;
    }

    public function getInputFilterSpecification(): array
    {
        return [
            self::INPUT_LIBELLE => [
                "name" => self::INPUT_LIBELLE,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_ANNEE => [
                "name" => self::INPUT_ANNEE,
                'required' => false,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            self::INPUT_NIVEAU => [
                "name" => self::INPUT_NIVEAU,
                'required' => false,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            self::INPUT_ETAT => [
                "name" => self::INPUT_ETAT,
                'required' => false,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}