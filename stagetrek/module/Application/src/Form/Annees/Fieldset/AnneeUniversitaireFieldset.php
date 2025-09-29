<?php


namespace Application\Form\Annees\Fieldset;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Element\TagSelectPicker;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Provider\Tag\CategorieTagProvider;
use Application\Provider\Tag\TagProvider;
use DateInterval;
use DateTime;
use DoctrineORMModule\Proxy\__CG__\UnicaenTag\Entity\Db\TagCategorie;
use Laminas\Feed\Reader\Collection\Category;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Date;
use Laminas\Validator\Callback;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class AnneeUniversitaireFieldset
 * @package Application\Form\Annees\Fieldset
 */
class AnneeUniversitaireFieldset extends AbstractEntityFieldset
    implements HasTagInputInterface
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use TagInputAwareTrait;

    const DATE_DEBUT = "dateDebut";
    const DATE_FIN = "dateFin";

    public function init(): static
    {
        $this->initIdInput();
        $this->initCodeInput();
        $this->initLibelleInput();
        $this->initDatesInputs();
        $this->initTagsInputs();
        return $this;
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
                                Callback::INVALID_VALUE => "Une année universitaire est déjà définie pour cette période",
                            ],
                            'callback' => function ($value, $context = []) {
                                $date = $context[self::DATE_DEBUT];
                                $date = DateTime::createFromFormat('Y-m-d', $date);
                                $code = $date->format('Y');
                                if($code == $context[self::CODE]){return true;}
                                $exist = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->findOneBy(['code' => $code]);
                                return !isset($exist);
                            }
                        ],
                    ],
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

    public function getTagsAvailables() : array
    {
        $tags = $this->getTagService()->getTags();
//        Cas spécial : Lock que l'on associe ici a la catégorie année
        foreach ($tags as $tag) {
            if($tag->getCode() == TagProvider::ETAT_LOCK){
                $catAnnee = $this->getObjectManager()->getRepository(TagCategorie::class)->findOneBy(['code' => CategorieTagProvider::ANNEE]);
                if(isset($catAnnee)){
                    $tag->setCategorie($catAnnee);
                }
            }
        }

        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if($c1->getId() !== $c2->getId()){
                //Trie spécifique : on met d'abord la catégorie Années
                if($c1->getCode()== CategorieTagProvider::ANNEE){return -1;}
                if($c2->getCode()== CategorieTagProvider::ANNEE){return 1;}
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