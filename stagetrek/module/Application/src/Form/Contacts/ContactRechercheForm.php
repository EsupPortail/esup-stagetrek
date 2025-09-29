<?php

namespace Application\Form\Contacts;

use Application\Form\Abstrait\AbstractRechercheForm;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use UnicaenTag\Entity\Db\Tag;

class ContactRechercheForm extends AbstractRechercheForm
    implements HasTagInputInterface
{
    use TagInputAwareTrait;
    use InputFilterProviderTrait;

    const INPUT_CODE = "code";
    const INPUT_LIBELLE = "libelle";
    const INPUT_DISPLAY_NAME = "nom";
    const INPUT_MAIL = "mail";
    const INPUT_TELEPHONE = "telephone";
    const INPUT_ACTIF = "actif";
    const INPUT_AFFICHER_CODE = "afficherCode";

    public function init(): static
    {
        parent::init();
        $this->initTagsInputs();
        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_CODE,
            'options' => [
                'label' => "Code",
            ],
            'attributes' => [
                'id' => self::INPUT_CODE,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'name' => self::INPUT_AFFICHER_CODE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Afficher les codes",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_AFFICHER_CODE,
                'value' => "false",
                'class' => 'form-check-input'
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_LIBELLE,
            'options' => [
                'label' => "Libellé / Fonction",
            ],
            'attributes' => [
                'id' => self::INPUT_LIBELLE,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_DISPLAY_NAME,
            'options' => [
                'label' => "Nom / Prénom",
            ],
            'attributes' => [
                'id' => self::INPUT_DISPLAY_NAME,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_MAIL,
            'options' => [
                'label' => "Mail",
            ],
            'attributes' => [
                'id' => self::INPUT_MAIL,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => self::INPUT_TELEPHONE,
            'options' => [
                'label' => "Téléphone",
            ],
            'attributes' => [
                'id' => self::INPUT_TELEPHONE,
                'placeholder' => "",
                'autofocus' => true,
            ],
        ]);

        $this->add([
            'name' => self::INPUT_ACTIF,
            'type' => Checkbox::class,
            'options' => [
                'label' => "N'afficher que les contacts actifs",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_ACTIF,
                'value' => "true",
                'class' => 'form-check-input'
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
            self::INPUT_DISPLAY_NAME => [
                "name" => self::INPUT_DISPLAY_NAME,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_MAIL => [
                "name" => self::INPUT_MAIL,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_TELEPHONE => [
                "name" => self::INPUT_TELEPHONE,
                'required' => false,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],
            self::INPUT_ACTIF => [
                "name" => self::INPUT_ACTIF,
                'required' => false,
                'filters' => [],
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
}

