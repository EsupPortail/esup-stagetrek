<?php


namespace Application\Form\Contacts\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\MailInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

class ContactFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use MailInputAwareTrait;

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
}