<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Validator\CodeValidatorAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */

trait CodeInputAwareTrait
{
//    Par défaut, le codeInput sera caché pour être généré automatiquement
//  Si l'on souhaite pouvoir le modifier, il faut surchager la méthode initCode
    protected function initCodeInput(bool $editable = false): static
    {
        if(!$editable)
        $this->add([
            "name" => DefaultInputKeyInterface::CODE,
            'type' => Hidden::class,
            'attributes' => [
                "id" => DefaultInputKeyInterface::CODE,
            ],
        ]);
        else{
            $this->add([
                "name" => DefaultInputKeyInterface::CODE,
                'type' => Text::class,
                'options' => [
                    'label' => $this->codeLabel,
                ],
                'attributes' => [
                    "id" => DefaultInputKeyInterface::CODE,
                    "placeholder" => $this->codePlaceHolder,
                ],
            ]);
        }

        $codeValidator = $this->getCodeValidator();

        $this->setInputfilterSpecification(DefaultInputKeyInterface::CODE, [
            'name' => DefaultInputKeyInterface::CODE,
            'required' =>false,
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
                        'min' => 0,
                        'max' => 64,
                    ],
                ],
                $codeValidator,
            ],
        ]);
        return $this;
    }

    protected string $codeLabel="Code";
    protected string  $codePlaceHolder="";

    /**
     * @return string
     */
    public function getCodeLabel(): string
    {
        return $this->codeLabel;
    }

    /**
     * @param string $codeLabel
     * @return \Application\Form\Misc\Traits\CodeInputAwareTrait
     */
    public function setCodeLabel(string $codeLabel): static
    {
        $this->codeLabel = $codeLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodePlaceHolder(): string
    {
        return $this->codePlaceHolder;
    }

    /**
     * @param string $codePlaceHolder
     * @return \Application\Form\Misc\Traits\CodeInputAwareTrait
     */
    public function setCodePlaceHolder(string $codePlaceHolder): static
    {
        $this->codePlaceHolder = $codePlaceHolder;
        return $this;
    }
    use CodeValidatorAwareTrait;
}