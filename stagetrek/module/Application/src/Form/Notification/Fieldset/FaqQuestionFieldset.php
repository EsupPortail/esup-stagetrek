<?php


namespace Application\Form\Notification\Fieldset;

use Application\Entity\Db\FaqCategorieQuestion;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;
use UnicaenApp\Service\EntityManagerAwareTrait;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Class FaqQuestionFieldset
 * @package Application\Form\Fieldset
 */
class FaqQuestionFieldset extends Fieldset
    implements
    InputFilterProviderInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    public function init() : static
    {
        $this->initIdInput();
        $this->initCategorieInput();
        $this->initQuestionInput();
        $this->initReponseInput();
        $this->initOrdreInput();
        $this->initRolesInput();
        return $this;
    }



    const ID = 'id';
    protected function initIdInput(): void
    {
        $this->add([
            'name' =>   self::ID,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::ID,
            ],
        ]);

        $this->setInputfilterSpecification(self::ID, [
            'name' =>self::ID,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const CATEGORIE = "categorie";
    protected function initCategorieInput(): void
    {

        $this->add([
            'type' => ObjectSelect::class,
            'name' => self::CATEGORIE,
            'options' => [
                'object_manager' => $this->getObjectManager(),
                'target_class'   => FaqCategorieQuestion::class,
                'label_generator' => function (FaqCategorieQuestion $category) {
                    return $category->getLibelle();
                },
                'find_method'    => [
                    'name'   => 'findBy',
                    'params' => [
                        'criteria' => [],

                        // Use key 'orderBy' if using ORM
                        'orderBy'  => ['ordre' => 'ASC'],
                    ],
                ],
                'label' => "Catégorie",
                'empty_option' => "Sélectionner une catégorie",

            ],
            'attributes' => [
                'id' => self::CATEGORIE,
                'class' =>  'selectpicker',
                'data-live-search' => true,
                'data-live-search-normalize' => true,
            ],
        ]);


        $this->setInputfilterSpecification(self::CATEGORIE , [
            "name" => self::CATEGORIE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }


    const QUESTION = "question";
    protected function initQuestionInput(): void
    {
        $this->add([
            "name" => self::QUESTION,
            'type' => Text::class,
            'options' => [
                'label' => 'Question',
            ],
            'attributes' => [
                "id" =>  self::QUESTION,
                "placeholder" => 'Saisir la question',
            ],
        ]);

        $this->setInputfilterSpecification(self::QUESTION, [
            'name' => self::QUESTION,
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
            ]
        ]);
    }

    const REPONSE = "reponse";
    protected function initReponseInput(): void
    {
        $this->add([
            "name" => self::REPONSE,
            'type' => Text::class,
            'options' => [
                'label' => 'Réponse',
            ],
            'attributes' => [
                "id" =>  self::REPONSE,
                "placeholder" => 'Saisir la reponse',
                "rows" => 5,
            ],
        ]);

        $this->setInputfilterSpecification(self::REPONSE, [
            'name' => self::REPONSE,
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
                    ],
                ],
            ]
        ]);
    }

    const ORDRE ='ordre';
    protected function initOrdreInput(): void
    {
        $this->add([
            "name" => self::ORDRE,
            'type' => Number::class,
            'options' => [
                'label' => "Ordre d'affichage",
            ],
            'attributes' => [
                "id" => self::ORDRE,
                "min" => 0,
            ],
        ]);

        $this->setInputfilterSpecification(self::ORDRE, [
            'name' => self::ORDRE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
            ],
        ]);
    }


    const ROLES = "roles";
    protected function initRolesInput(): void
    {
        $this->add([
            'type' => ObjectSelect::class,
            'name' => self::ROLES,
            'options' => [
                'object_manager' => $this->getObjectManager(),
                'target_class'   => Role::class,
                'label_generator' => function (Role $role) {
                    return $role->getLibelle();
                },
                'find_method'    => [
                    'name'   => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy'  => ['libelle' => 'ASC'],
                    ],
                ],
                'label' => "Uniquement visible pour les roles",
            ],
            'attributes' => [
                'multiple' => 'multiple',
                'autofocus' => true,
                'data-tick-icon' => "fas fa-check text-primary",
                'data-selected-text-format' =>'count > 3',
                'data-count-selected-text' => '{0} roles selectionnés',
                'data-actions-box'=>true,
                'data-select-all-text'=>"Tout les rôles 
                    <span class='text-small text-muted'>(Utilisateurs connectées)</span>",
                'data-deselect-all-text'=>"Aucun rôles
                    <span class='text-small text-muted'>(Question / Réponse publique)</span>",
                'title'=> 'Question / Réponse publique',
                'data-live-search'=>false,
                'id' => self::ROLES,
                'class' =>  'selectpicker',
            ],
        ]);

        $this->setInputfilterSpecification(self::ROLES, [
            "name" => self::ROLES,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    /** @var array $inputFilterSpecification */
    protected array $inputFilterSpecification = [];
    /**
     * @return array
     */
    public function getInputFilterSpecification(): array
    {
        return $this->inputFilterSpecification;
    }

    public function setInputfilterSpecification(string $inputId, array $specification) : static
    {
        $this->inputFilterSpecification[$inputId] = $specification;
        return $this;
    }
}