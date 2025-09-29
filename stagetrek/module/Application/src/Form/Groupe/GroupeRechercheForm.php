<?php

namespace Application\Form\Groupe;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireEtatSelectPicker;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class GroupeRechercheForm
 * @package Application\Form\Etudiant
 */
class GroupeRechercheForm extends AbstractRechercheForm
implements HasTagInputInterface
{
    const INPUT_LIBELLE = "libelle";
    const INPUT_ANNEE = "annee_universitaire";
    const INPUT_NIVEAU = "niveau_etude";
    const INPUT_ETAT ="annee_etat";

    use TagInputAwareTrait;
    use InputFilterProviderTrait;

    public function init(): static
    {
        parent::init();
        $this->initTagsInputs();

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
                if($c1->getCode()== CategorieTagProvider::GROUPE){return -1;}
                if($c2->getCode()== CategorieTagProvider::GROUPE){return 1;}
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