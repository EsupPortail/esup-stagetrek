<?php


namespace Application\Form\Contacts\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\MailInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Provider\Tag\CategorieTagProvider;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;
use UnicaenTag\Entity\Db\HasTagsTrait;
use UnicaenTag\Entity\Db\Tag;

class ContactFieldset extends AbstractEntityFieldset
    implements HasTagInputInterface
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use MailInputAwareTrait;
    use TagInputAwareTrait;

    public function init() : static
    {
        $this->setLibelleLabel("Libellé / Fonction");
        $this->getLibelleValidator()->setUnique(false);
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initMailInput();
        $this->initDisplayNameInput();
        $this->initTelephoneInput();
        $this->initEtatInput();
        $this->initTagsInputs();
        return $this;
    }

    const DISPLAY_NAME = "displayName";
    private function initDisplayNameInput() : void
    {
        $this->add([
            "name" => self::DISPLAY_NAME,
            'type' => Text::class,
            'options' => [
                'label' => "Nom, Prénom",
            ],
            'attributes' => [
                "id" => self::DISPLAY_NAME,
                "placeholder" => "Saisir un nom et un prénom",
            ],
        ]);

        $this->setInputfilterSpecification(self::DISPLAY_NAME, [
            "name" => self::DISPLAY_NAME,
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

    const TELEPHONE = "telephone";
    private function initTelephoneInput() : void
    {
        $this->add([
            "name" => self::TELEPHONE,
            'type' => Text::class,
            'options' => [
                'label' => "Numéro de téléphone",
            ],
            'attributes' => [
                "id" => self::TELEPHONE,
                "placeholder" => "Saisir un numéro de téléphone",
            ],
        ]);

        $this->setInputfilterSpecification(
                self::TELEPHONE, [
            "name" => self::TELEPHONE,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
    }

    const ACTIF = "actif";
    private function initEtatInput() : void
    {
        $this->add([
            'name' => self::ACTIF,
            'type' => Checkbox::class,
            'options' => [
                'label' =>  "Contact actif",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::ACTIF,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->setInputfilterSpecification(self::ACTIF, [
            "name" => self::ACTIF,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
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
}