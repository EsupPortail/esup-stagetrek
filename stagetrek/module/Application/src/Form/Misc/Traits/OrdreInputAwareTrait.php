<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Number;

trait OrdreInputAwareTrait
{
    protected function initOrdreInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::ORDRE,
            'type' => Number::class,
            'options' => [
                'label' => $this->ordreLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::ORDRE,
                "min" => 1,
            ],
        ]);

        $this->setInputfilterSpecification(DefaultInputKeyInterface::ORDRE, [
            'name' => DefaultInputKeyInterface::ORDRE,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
            'validators' => [
            ],
        ]);
        return $this;
    }

    protected string $ordreLabel="Ordre";
    /**
     * @return string
     */
    public function getOrdreLabel(): string
    {
        return $this->ordreLabel;
    }

    /**
     * @param string $ordreLabel
     * @return \Application\Form\Misc\Traits\OrdreInputAwareTrait
     */
    public function setOrdreLabel(string $ordreLabel): static
    {
        $this->ordreLabel = $ordreLabel;
        return $this;
    }
}