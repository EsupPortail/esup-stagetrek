<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */

trait IdInputAwareTrait
{
    protected function initIdInput(): static
    {
        $this->add([
            'name' => DefaultInputKeyInterface::ID,
            'type' => Hidden::class,
            'attributes' => [
                'id' => DefaultInputKeyInterface::ID,
            ],
        ]);

        $this->setInputfilterSpecification(DefaultInputKeyInterface::ID, [
            'name' => DefaultInputKeyInterface::ID,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        return $this;
    }
}