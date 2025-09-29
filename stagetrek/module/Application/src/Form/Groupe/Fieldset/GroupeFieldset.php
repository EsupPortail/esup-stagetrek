<?php


namespace Application\Form\Groupe\Fieldset;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\ReferentielPromo;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Application\Form\Referentiel\Element\ReferentielPromoSelectPicker;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Text;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class GroupeFieldset
 * @package Application\Form\Groupe\Fieldset
 */
class GroupeFieldset extends AbstractEntityFieldset
    implements HasTagInputInterface
{
    use IdInputAwareTrait;
    use LibelleInputAwareTrait;
    use CodeInputAwareTrait;
    use TagInputAwareTrait;


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function init() : static
    {
        $this->initCodeInput(true);
        $this->initIdInput();
        $this->initLibelleInput();
        $this->initAnneeInput();
        $this->initNiveauInput();
        $this->initReferentielsInput();
        $this->initTagsInputs();
        return $this;

    }

    //On ne peux pas utiliser le libellé validator classique car le nom du groupe est dépendant de l'année
    protected function initLibelleInput(): static
    {
        $this->add([
            "name" => self::LIBELLE,
            'type' => Text::class,
            'options' => [
                'label' => $this->libelleLabel,
            ],
            'attributes' => [
                "id" => self::LIBELLE,
                "placeholder" => $this->libellePlaceHolder,
            ],
        ]);
        $this->setInputfilterSpecification(self::LIBELLE, [
            'name' => self::LIBELLE,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Un groupe portant ce libellé existe déjà",
                        ],
                        'callback' => function ($value, $context = []) {
                            $libelle = $context[self::LIBELLE];
                            $annee = $context[self::ANNEE_UNIVERSITAIRE];
                            $id = $context[self::ID];
                            if (!$annee) {
                                return true;
                            } //Erreur mais pas du fait du libellé
                            /** @var Groupe[] $groupes */
                            $groupes = $this->getObjectManager()->getRepository(Groupe::class)->findBy(['libelle' => $libelle, 'anneeUniversitaire' => $annee], []);
                            if (empty($groupes)) return true;
                            if (sizeof($groupes) > 1) return false;
                            return ($groupes[0]->getId() == $id);
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ]
        ]);
        return $this;
    }

    const NIVEAU_ETUDE = "niveau_etude";
    private function initNiveauInput() : void
    {
        $this->add([
            'type' => NiveauEtudeSelectPicker::class,
            'name' => self::NIVEAU_ETUDE,
            'options' => [
                'label' => "Niveau d'étude",
                "empty_option" => "Sélectionner un niveau d'étude",
            ],
            'attributes' => [
                'id' => self::NIVEAU_ETUDE,
            ],
        ]);
        $this->setInputfilterSpecification(self::NIVEAU_ETUDE, [
            'name' => self::NIVEAU_ETUDE,
            'required' => true
        ]);
    }

    const ANNEE_UNIVERSITAIRE = "annee_universitaire";
    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function initAnneeInput() : void
    {
        $this->add([
            'type' => AnneeUniversitaireSelectPicker::class,
            'name' => self::ANNEE_UNIVERSITAIRE,
            'options' => [
                'label' => "Année",
                "empty_option" => "Sélectionner une année universitaire",
            ],
            'attributes' => [
                'id' => self::ANNEE_UNIVERSITAIRE,
            ],
        ]);
        $this->setAnneeUniversitaire();//par défaut pas d'année
        $this->setInputfilterSpecification(self::ANNEE_UNIVERSITAIRE, [
            'name' => self::ANNEE_UNIVERSITAIRE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "L'année universitaire demandé n'a pas été trouvée",
                        ],
                        'callback' => function ($value) {
                            $annee = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($value);
                            return $annee != null;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Impossible d'ajouter un groupe d'étudiants à une année validée",
                        ],
                        'callback' => function ($value, $context = []) {
                            if (key_exists(self::ID, $context) &&
                                $context[self::ID] != ''
                            ) {
                                return true; //On ne vérifie pas pour l'édition que l'année est validée mais uniquement pour l'ajout  d'un nouveau groupe
                            }
                            /** @var AnneeUniversitaire $annee */
                            $annee = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($value);
                            if (!$annee) return true; //Erreur mais pas du fait l'année non trouvé
                            return !$annee->isLocked();
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);
    }


    public function getTagsAvailables(): array
    {
        $tags = $this->getTagService()->getTags();
        usort($tags, function (Tag $t1, Tag $t2) {
            $c1 = $t1->getCategorie();
            $c2 = $t2->getCategorie();
            if ($c1->getId() !== $c2->getId()) {
                //Trie spécifique : on met d'abord la catégorie Années
                if ($c1->getCode() == CategorieTagProvider::CONTACT_STAGE) {
                    return -1;
                }
                if ($c2->getCode() == CategorieTagProvider::CONTACT_STAGE) {
                    return 1;
                }
                if ($c1->getOrdre() < $c2->getOrdre()) return -1;
                if ($c2->getOrdre() < $c1->getOrdre()) return 1;
                return ($c1->getId() < $c2->getId()) ? -1 : 1;
            }
            if ($t1->getOrdre() < $t2->getOrdre()) return -1;
            if ($t2->getOrdre() < $t1->getOrdre()) return 1;
            return ($t1->getId() < $t2->getId()) ? -1 : 1;
        });
        return $tags;
    }

    /**
     * @param \Application\Entity\Db\AnneeUniversitaire|null $annee
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function setAnneeUniversitaire(?AnneeUniversitaire $annee=null) : static
    {
        /** @var AnneeUniversitaireSelectPicker $input */
        $input = $this->get(self::ANNEE_UNIVERSITAIRE);
        if(isset($annee)){
            $annees=[$annee];
            $input->setAttribute('data-live-search', false);
            $input->setOption('empty_option', null);
            $input->setEmptyOption(null);
        }
        else{
            $annees = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->findBy([],['dateFin' => 'desc', 'dateDebut' => 'desc']);
        }
        $input->setAnneesUniversitaires($annees);
        //Rajout de l'attribut disabled pour les années universitaires vérouillez
        /** @var AnneeUniversitaire $a */
        foreach ($annees as $a){
            if($a->isLocked()) {
                if(isset($annee) && $annee->getId()==$a->getId()){
                    continue;
                }
                $input->setAnneeUniversitaireAttribute($a, 'data-icon', 'fas fa-lock text-muted');
                $input->setAnneeUniversitaireAttribute($a, 'disabled', 'true');
            }
        }
        return $this;
    }


    const REFERENTIELS = "referentiels";
    public function initReferentielsInput() : void
    {
        $this->add([
            'type' => ReferentielPromoSelectPicker::class,
            'name' => self::REFERENTIELS,
            'options' => [
                'label' => 'Référentiels associées',
            ],
            'attributes' => [
                'id' => self::REFERENTIELS,
                "class" => 'selectpicker',
                "data-live-search" => true,
                "data-live-search-normalize" => true,
                "multiple" => "multiple",
                "data-tick-icon" => "fas fa-check text-primary",
                "title" => "Sélectionnez des référentiels",
                "data-selected-text-format" => "count > 2",
                "data-count-selected-text" => "{0} référentiels",
            ],
        ]);

        $this->setInputfilterSpecification(self::REFERENTIELS,  [
            "name" => self::REFERENTIELS,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }
}