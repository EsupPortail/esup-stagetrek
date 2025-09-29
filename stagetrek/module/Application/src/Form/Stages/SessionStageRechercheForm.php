<?php

namespace Application\Form\Stages;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Form\Stages\Element\SessionStageEtatSelectPicker;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;
use UnicaenTag\Entity\Db\HasTagsTrait;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class SessionStageRechercheForm
 * @package Application\Form\SessionsStages
 */
class SessionStageRechercheForm extends AbstractRechercheForm
implements HasTagInputInterface
{

    use TagInputAwareTrait;
    use InputFilterProviderTrait;

    const INPUT_LIBELLE = "libelle";
    const INPUT_ANNEE = "annee_universitaire";
    const INPUT_GROUPE = "groupe";
    const INPUT_ETAT="etat";

    public function init() : static
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
                if($c1->getCode()== CategorieTagProvider::SESSION_STAGE
                    || $c1->getCode()== CategorieTagProvider::STAGE
                ){return -1;}
                if($c2->getCode()== CategorieTagProvider::SESSION_STAGE
                || $c2->getCode()== CategorieTagProvider::STAGE
                ){return 1;}
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