<?php

namespace Application\Form\Etudiant;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Etudiant\Element\EtudiantEtatSelectPicker;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;

/**
 * Class EtudiantForm
 * @package Application\Form\Etudiant
 */
class EtudiantRechercheForm extends AbstractRechercheForm
{
    const INPUT_NOM="nom";
    const INPUT_PRENOM="prenom";
    const INPUT_NUM_ETU="numEtu";
    const INPUT_ANNEE="anneeUniversitaire";
    const INPUT_GROUPE="groupe";
    const INPUT_ETAT="etat";

    public function init(): void
    {
        parent::init();

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_NOM,
            'options' => [
                'label' => "Nom",
            ],
            'attributes' => [
                'id' => self::INPUT_NOM,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_PRENOM,
            'options' => [
                'label' => "Prénom",
            ],
            'attributes' => [
                'id' => self::INPUT_PRENOM,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_NUM_ETU,
            'options' => [
                'label' => "Numéro d'étudiant",
            ],
            'attributes' => [
                'id' => self::INPUT_NUM_ETU,
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
        /** @var AnneeUniversitaireSelectPicker $selectAnnee */
        $selectAnnee = $this->get(self::INPUT_ANNEE);
        $selectAnnee->setNoAnneeOption("Etudiants non inscrits dans une année");


        $this->add([
            'type' => GroupeSelectPicker::class,
            'name' => self::INPUT_GROUPE,
            'options' => [
                'label' => 'Groupe',
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
        /** @var GroupeSelectPicker $selectGroupe */
        $selectGroupe = $this->get(self::INPUT_GROUPE);
        $selectGroupe->setNoGroupeOption("Etudiants non inscrits dans un groupe");

        $this->add([
            'type' => EtudiantEtatSelectPicker::class,
            'name' => self::INPUT_ETAT,
            'options' => [
                'label' => "État du cursus",
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
                "data-actions-box" => "true",
                "data-select-all-text" => "Tout selectionner",
                "data-deselect-all-text" => "Tout déselectionner",
            ],
        ]);
    }

    public function getInputFilterSpecification() : array
    {
        return [
            self::INPUT_NOM => [
                "name" => self::INPUT_NOM,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_PRENOM => [
                "name" => self::INPUT_PRENOM,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_NUM_ETU => [
                "name" => self::INPUT_NUM_ETU,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
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