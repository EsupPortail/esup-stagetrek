<?php

namespace Application\Form\Contacts;

use Application\Form\Abstrait\AbstractRechercheForm;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;

class ContactRechercheForm extends AbstractRechercheForm
{
    const INPUT_CODE = "code";
    const INPUT_LIBELLE = "libelle";
    const INPUT_DISPLAY_NAME = "nom";
    const INPUT_MAIL = "mail";
    const INPUT_TELEPHONE = "telephone";
    const INPUT_ACTIF = "actif";
    const INPUT_AFFICHER_CODE = "afficherCode";

    public function init(): void
    {
        parent::init();
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
        ];
    }
}