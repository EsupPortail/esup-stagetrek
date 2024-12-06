<?php

namespace Application\Form\Stages;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Stages\Element\SessionStageEtatSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;

/**
 * Class SessionStageRechercheForm
 * @package Application\Form\SessionsStages
 */
class SessionStageRechercheForm extends AbstractRechercheForm
{
    const INPUT_LIBELLE = "libelle";
    const INPUT_ANNEE = "annee_universitaire";
    const INPUT_GROUPE = "groupe";
    const INPUT_ETAT="etat";

    public function init(): void
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
                "data-actions-box" => "true",
                "data-select-all-text" => "Tout selectionner",
                "data-deselect-all-text" => "Tout déselectionner",
            ],
        ]);
        $this->add([
            'type' => GroupeSelectPicker::class,
            'name' => self::INPUT_GROUPE,
            'options' => [
                'label' => "Groupe",
            ],
            'attributes' => [
                'id' => self::INPUT_GROUPE,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner un groupe",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} groupes selectionnés",
                "data-actions-box" => "true",
                "data-select-all-text" => "Tout selectionner",
                "data-deselect-all-text" => "Tout déselectionner",
            ],
        ]);
        $this->add([
            'type' => SessionStageEtatSelectPicker::class,
            'name' => self::INPUT_ETAT,
            'options' => [
                'label' => "État de la session",
            ],
            'attributes' => [
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionner un état",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} etats selectionnés",
                "data-actions-box" => "true",
                "data-select-all-text" => "Tout selectionner",
                "data-deselect-all-text" => "Tout déselectionner",
            ],
        ]);

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
            self::INPUT_GROUPE => [
                "name" => self::INPUT_GROUPE,
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