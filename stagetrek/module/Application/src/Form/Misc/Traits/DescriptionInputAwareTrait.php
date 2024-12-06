<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\StringLength;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */
trait DescriptionInputAwareTrait
{
    protected function initDescriptionInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::DESCRIPTION,
            'type' => TextArea::class,
            'options' => [
                'label' => $this->descriptionLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::DESCRIPTION,
                "placeholder" => $this->descriptionPlaceHolder,
                "row" => $this->descriptionRow,
            ],
        ]);

        $options = [];
        $options['encoding'] = "UTF-8";
        if(isset($this->descriptionMinSize)){
            $options['min'] = $this->descriptionMinSize ;
        }
        if(isset($this->descriptionMaxSize) && $this->descriptionMaxSize>0){
            $options['max'] = $this->descriptionMaxSize ;
        }

        $validators = [
            [
                'name' => StringLength::class,
                'options' => $options,
            ],
        ];
        $this->setInputfilterSpecification(DefaultInputKeyInterface::DESCRIPTION, [
            'name' => DefaultInputKeyInterface::DESCRIPTION,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => $validators
        ]);
        return $this;
    }

    protected string $descriptionLabel="Description";
    protected string  $descriptionPlaceHolder="";

    public function getDescriptionLabel(): string
    {
        return $this->descriptionLabel;
    }

    public function setDescriptionLabel(string $descriptionLabel): static
    {
        $this->descriptionLabel = $descriptionLabel;
        return $this;
    }

    public function getDescriptionPlaceHolder(): string
    {
        return $this->descriptionPlaceHolder;
    }

    public function setDescriptionPlaceHolder(string $descriptionPlaceHolder): static
    {
        $this->descriptionPlaceHolder = $descriptionPlaceHolder;
        return $this;
    }


    protected int $descriptionRow = 5;
    protected ?int $descriptionMinSize = null;
    protected ?int $descriptionMaxSize = null;

    public function getDescriptionRow(): int
    {
        return $this->descriptionRow;
    }

    public function setDescriptionRow(int $descriptionRow): void
    {
        $this->descriptionRow = $descriptionRow;
    }

    public function getDescriptionMinSize(): ?int
    {
        return $this->descriptionMinSize;
    }

    public function setDescriptionMinSize(?int $descriptionMinSize): void
    {
        $this->descriptionMinSize = $descriptionMinSize;
    }

    public function getDescriptionMaxSize(): ?int
    {
        return $this->descriptionMaxSize;
    }

    public function setDescriptionMaxSize(?int $descriptionMaxSize): void
    {
        $this->descriptionMaxSize = $descriptionMaxSize;
    }





}