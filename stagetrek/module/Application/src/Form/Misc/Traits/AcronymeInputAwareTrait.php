<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Validator\AcronymeValidatorAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */
trait AcronymeInputAwareTrait
{
    use AcronymeValidatorAwareTrait;

    protected function initAcronymeInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::ACRONYME,
            'type' => Text::class,
            'options' => [
                'label' => $this->acronymeLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::ACRONYME,
                "placeholder" => $this->acronymePlaceholder,
            ],
        ]);

        $acronymeValidator = $this->getAcronymeValidator();
        $validators = [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 1,
                    'max' => 128,
                ],
            ],
        ];
        if(isset($acronymeValidator)){
            $validators[] = $acronymeValidator;
        }
        $this->setInputfilterSpecification(DefaultInputKeyInterface::ACRONYME, [
            'name' => DefaultInputKeyInterface::ACRONYME,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => $validators
        ]);
        return $this;
    }

    protected string $acronymeLabel="Acronyme";
    protected string  $acronymePlaceholder="";

    /**
     * @return string
     */
    public function getAcronymeLabel(): string
    {
        return $this->acronymeLabel;
    }

    /**
     * @param string $acronymeLabel
     * @return \Application\Form\Misc\Traits\AcronymeInputAwareTrait
     */
    public function setAcronymeLabel(string $acronymeLabel): static
    {
        $this->acronymeLabel = $acronymeLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getAcronymePlaceholder(): string
    {
        return $this->acronymePlaceholder;
    }

    /**
     * @param string $acronymePlaceholder
     * @return AcronymeInputAwareTrait
     */
    public function setAcronymePlaceholder(string $acronymePlaceholder): static
    {
        $this->acronymePlaceholder = $acronymePlaceholder;
        return $this;
    }
}