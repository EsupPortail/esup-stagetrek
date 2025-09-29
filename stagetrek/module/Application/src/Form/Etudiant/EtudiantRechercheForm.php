<?php

namespace Application\Form\Etudiant;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Etudiant\Element\EtudiantEtatSelectPicker;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class EtudiantForm
 * @package Application\Form\Etudiant
 */
class EtudiantRechercheForm extends AbstractRechercheForm
    implements HasTagInputInterface
{
    const INPUT_NOM="nom";
    const INPUT_PRENOM="prenom";
    const INPUT_NUM_ETU="numEtu";
    const INPUT_ANNEE="anneeUniversitaire";
    const INPUT_GROUPE="groupe";
    const INPUT_ETAT="etat";

    use TagInputAwareTrait;
    use InputFilterProviderTrait;

    public function init(): static
    {
        parent::init();
        $this->initTagsInputs();

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
        return $this;
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

            self::TAGS => [
                "name" => self::TAGS,
                'required' => false,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }

    public function getTagsAvailables() : array
    {
        $tags = $this->getTagService()->getTags();

        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if($c1->getId() !== $c2->getId()){
                //Trie spécifique : on met d'abord la catégorie Années
                if($c1->getCode()== CategorieTagProvider::ETUDIANT){return -1;}
                if($c2->getCode()== CategorieTagProvider::ETUDIANT){return 1;}
                if($c1->getOrdre() < $c2->getOrdre()) return -1;
                if($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if($t1->getOrdre() < $t2->getOrdre()) return -1;
            if($t2->getOrdre() < $t1->getOrdre()) return 1;
            return ($t1->getId() < $t2->getId()) ? -1 : 1;
        });
        return $tags;
    }
}