<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Validator\LibelleValidatorAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */
trait LibelleInputAwareTrait
{
    use LibelleValidatorAwareTrait;

    protected function initLibelleInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::LIBELLE,
            'type' => Text::class,
            'options' => [
                'label' => $this->libelleLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::LIBELLE,
                "placeholder" => $this->libellePlaceHolder,
            ],
        ]);

        $libelleValidator = $this->getLibelleValidator();
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
        if(isset($libelleValidator)){
            $validators[] = $libelleValidator;
        }
        $this->setInputfilterSpecification(DefaultInputKeyInterface::LIBELLE, [
            'name' => DefaultInputKeyInterface::LIBELLE,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => $validators
        ]);
        return $this;
    }

    protected string $libelleLabel="Libellé";
    protected string  $libellePlaceHolder="";

    /**
     * @return string
     */
    public function getLibelleLabel(): string
    {
        return $this->libelleLabel;
    }

    /**
     * @param string $libelleLabel
     * @return \Application\Form\Misc\Traits\LibelleInputAwareTrait
     */
    public function setLibelleLabel(string $libelleLabel): static
    {
        $this->libelleLabel = $libelleLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibellePlaceHolder(): string
    {
        return $this->libellePlaceHolder;
    }

    /**
     * @param string $libellePlaceHolder
     * @return \Application\Form\Misc\Traits\LibelleInputAwareTrait
     */
    public function setLibellePlaceHolder(string $libellePlaceHolder): static
    {
        $this->libellePlaceHolder = $libellePlaceHolder;
        return $this;
    }
}