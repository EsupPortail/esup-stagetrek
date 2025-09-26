<?php


namespace Application\Form\Etudiant\Fieldset;

use Application\Entity\Db\Etudiant;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\MailInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;

/**
 * Class EtudiantFieldset
 * @package Application\Form\Fieldset
 */
class EtudiantFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    use MailInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initUserInput();

        $this->initNomInput();
        $this->initPrenomInput();
        $this->initNumEtuInput();
        $this->initDateNaissanceInput();
        $this->initMailInput();

        $this->initAdresseFielset();
        return $this;
    }


    const USER = "user";
    protected function initUserInput(): static
    {
        $this->add([
            'name' => self::USER,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::USER,
            ],
        ]);

        $this->setInputfilterSpecification(self::USER, [
            'name' => self::USER,
            'required' => false,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
            'validators' => [
            ],
        ]);
        return $this;
    }

    const NUM_ETU = "numEtu";
    protected function initNumEtuInput(): static
    {
        $this->add([
            'name' => self::NUM_ETU,
            'type' => Text::class,
            'options' => [
                'label' => "Numéro d'étudiant",
            ],
            'attributes' => [
                'id' => self::NUM_ETU,
                'placeholder' => "Numéro d'étudiant",
            ],
        ]);

        $this->setInputfilterSpecification(self::NUM_ETU, [
            'name' => self::NUM_ETU,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 25,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Ce numéro d'étudiant est déjà utilisé.",
                        ],
                        'callback' => function ($value, $context = []) {
                            $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->findOneBy(['numEtu' => $value]);
                            if(!isset($etudiant)){return true;}
                            $id = ($context[self::ID]) ? intval($context[self::ID]) : null ;
                            return $etudiant->getId() == $id;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);

        return $this;
    }

    const NOM = "nom";
    protected function initNomInput(): static
    {
        $this->add([
            'name' => self::NOM,
            'type' => Text::class,
            'options' => [
                'label' => "Nom",
            ],
            'attributes' => [
                'id' => self::NOM,
            ],
        ]);

        $this->setInputfilterSpecification(self::NOM, [
            'name' => self::NOM,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);

        return $this;
    }

    const PRENOM = "prenom";
    protected function initPrenomInput(): static
    {
        $this->add([
            'name' => self::PRENOM,
            'type' => Text::class,
            'options' => [
                'label' => "Prénom",
            ],
            'attributes' => [
                'id' => self::PRENOM,
                'class' => 'auto-complete-field',
            ],
        ]);

        $this->setInputfilterSpecification(self::PRENOM, [
            'name' => self::PRENOM,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);

        return $this;
    }

//    Surcharge de mail car c'est par lui que l'on retrouvera l'utilisateur
    protected function initMailInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::MAIL,
            'type' => Text::class,
            'options' => [
                'label' => $this->mailLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::MAIL,
                "placeholder" => $this->mailPlaceHolder,
            ],
        ]);

        $this->setInputfilterSpecification(DefaultInputKeyInterface::MAIL, [
            'name' => DefaultInputKeyInterface::MAIL,
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
                        'max' => 64,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "L'adresse mail n'est pas valide.",
                        ],
                        'callback' => function ($value) {
                            $mail = trim($value);
                            if ($mail == "") {
                                return true;
                            }
                            return filter_var($mail, FILTER_VALIDATE_EMAIL);
                        },
                        'break_chain_on_failure' => false,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Cette adresse mail mail est déjà utilisé.",
                        ],
                        'callback' => function ($value, array $context = []) {
                            $mail = trim($value);
                            $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->findOneBy(['email' => $mail]);
                            if(!isset($etudiant)){return true;}
                            $id = ($context[self::ID]) ? intval($context[self::ID]) : null ;
                            return $etudiant->getId() == $id;
                        },
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
        return $this;
    }


    const DATE_NAISSANCE = "dateNaissance";
    protected function initDateNaissanceInput(): static
    {
        $this->add([
            'name' => self::DATE_NAISSANCE,
            'type' => Date::class,
            'options' => [
                'label' => "Date de naissance",
            ],
            'attributes' => [
                'id' => self::DATE_NAISSANCE,
            ],
        ]);

        $this->setInputfilterSpecification(self::DATE_NAISSANCE, [
            'name' => self::DATE_NAISSANCE,
            'required' => false,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);

        return $this;
    }

    const ADRESSE = "adresse";
    protected function initAdresseFielset(): static
    {
        $adresseFieldset = $this->getFormFactory()->getFormElementManager()->get(AdresseFieldset::class);
        $this->add($adresseFieldset);
        return $this;
    }

}