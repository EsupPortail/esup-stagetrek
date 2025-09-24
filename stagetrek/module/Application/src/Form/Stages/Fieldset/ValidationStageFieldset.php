<?php


namespace Application\Form\Stages\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\Callback;

/**
 * Class ValidationStageFieldset
 * @package Application\Form\Stages\Fieldset
 */
class ValidationStageFieldset  extends AbstractEntityFieldset
{
    use IdInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initEtatValidationInput();
        $this->initWarningInput();
        $this->initValidateByInput();
        $this->initComentairesInputs();
        return $this;

    }
    const INPUT_ETAT_VALIDATION = "etatValidation";

    const VALIDATION_ETAT_VALIDER = 1;
//    TODO : revoir le WARNING POUR qu'il soit indépendant de validé / non validé
    const VALIDATION_ETAT_WARNING = 2;
    const VALIDATION_ETAT_INVALIDER = -1;
    const VALIDATION_ETAT_NON_DEFINI = 0;

    private function initEtatValidationInput() : void
    {

        $valueOptions = [
            self::VALIDATION_ETAT_VALIDER => '<span class="ms-1 me-3">Oui</span>',
//            self::VALIDATION_ETAT_WARNING => '<span class="ms-1 me-3">Oui, mais je souhaite porter à la connaissance de l\'UFR certains éléments.</span>',
            self::VALIDATION_ETAT_INVALIDER => '<span class="ms-1 me-3">Non</span>',
        ];
        if($this->modeAdmin){
            $valueOptions[self::VALIDATION_ETAT_NON_DEFINI] = '<span class="ms-1 me-3">Validation non effectuée</span>';
        }

        $this->add([
            'name' => self::INPUT_ETAT_VALIDATION,
            'type' => Radio::class,
            'options' => [
                'label' => "Valider le stage ?",
                'label_options' => [
                    'disable_html_escape' => true,
                ],
                'use_hidden_element' => true,
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'id' => self::INPUT_ETAT_VALIDATION,
                'value' => self::VALIDATION_ETAT_NON_DEFINI,
            ]
        ]);

        $this->setInputfilterSpecification(self::INPUT_ETAT_VALIDATION, [
            "name" => self::INPUT_ETAT_VALIDATION,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const INPUT_WARNING = "warning";
    private function initWarningInput() : void
    {
        $label = ($this->modeAdmin) ? "Signalement d'un problème sur le stage"
            : "Je souhaite porter à la connaissance de l'UFR certains éléments";
        $this->add([
            'name' => self::INPUT_WARNING,
            'type' => Checkbox::class,
            'options' => [
                'label' => sprintf('<span class="ms-1 me-3">%s</span>', $label) ,
                'use_hidden_element' => true,
                'disable_html_escape' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_WARNING,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->setInputfilterSpecification(self::INPUT_WARNING, [
            "name" => self::INPUT_WARNING,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    const INPUT_VALIDATE_BY ='validateBy';
    protected function initValidateByInput(): static
    {
        if($this->modeAdmin){
            $this->add([
                'name' => self::INPUT_VALIDATE_BY,
                'type' => Text::class,
                'options' => [
                    'label' => "Validation effectuée par",
                ],
                'attributes' => [
                    'id' => self::INPUT_VALIDATE_BY,
                    'value' => $this->validateBy,
                ],
            ]);
        }
        else{
            $this->add([
                'name' => self::INPUT_VALIDATE_BY,
                'type' => Hidden::class,
                'attributes' => [
                    'id' => self::INPUT_VALIDATE_BY,
                    'value' => $this->validateBy,
                ],
            ]);
        }

        $this->setInputfilterSpecification(self::INPUT_VALIDATE_BY, [
            'name' => self::INPUT_VALIDATE_BY,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        return $this;
    }

    const INPUT_COMMENTAIRES = "commentaire";
    const INPUT_COMMENTAIRES_CACHE = "commentaireCache";
    private function initComentairesInputs() : void
    {
        $this->add([
            'type' => TextArea::class,
            'name' => self::INPUT_COMMENTAIRES,
            'options' => [
                'label' => "Eléments à l'attention de l'étudiant",
            ],
            'attributes' => [
                "id" => self::INPUT_COMMENTAIRES,
                "placeholder" => "Eleménents apportés à l'attention de l'étudiant",
                "rows" => 5,
                'class' => "no-resize",
            ],
        ]);

        $this->add([
            'type' => TextArea::class,
            'name' => self::INPUT_COMMENTAIRES_CACHE,
            'options' => [
                'label' => "Eléments à l'attention de l'UFR",
            ],
            'attributes' => [
                "id" => self::INPUT_COMMENTAIRES_CACHE,
                "placeholder" => "Eleménents apportés à l'attention de l'UFR",
                "rows" => 5,
                'class' => "no-resize",
            ],
        ]);

        $this->setInputfilterSpecification(self::INPUT_COMMENTAIRES, [
            "name" => self::INPUT_COMMENTAIRES,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::INPUT_COMMENTAIRES_CACHE, [
            "name" => self::INPUT_COMMENTAIRES_CACHE,
            'required' => false,
            'allow_empty' => false,
            'continue_if_empty' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "Merci de fournir des informations à l'attention de la commission.",
                    ],
                    'callback' => function ($value, $context = []) {
                        if($value != ""){return true;}
                        if($this->modeAdmin){return true;}
                        return intval($context[self::INPUT_WARNING]) == 0;
                    }
                ],
            ]]
        ]);
    }


    protected bool $modeAdmin = false;
    public function setModeAdmin(?bool $modeAdmin) : static
    {
        $this->modeAdmin = $modeAdmin;
        $this->initEtatValidationInput();
        $this->initWarningInput();
        $this->initValidateByInput();
        return $this;
    }

    public function getModeAdmin() : bool
    {
        return $this->modeAdmin;
    }

    protected ?string $validateBy = null;
    public function setValidateBy(?string $validateBy) : static
    {
        $this->validateBy = $validateBy;
        $this->initValidateByInput();
        return $this;
    }

}