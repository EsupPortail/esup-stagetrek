<?php


namespace Application\Form\Parametre\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;

/**
 * Class ParametreCoutAffectationFieldset
 * @package Application\Form\Fieldset
 */
class ParametreCoutAffectationFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;

    public function init(): void
    {
        $this->initIdInput();
        $this->initRangInput();
        $this->initCoutInput();
    }


    const RANG = "rang";
    protected function initRangInput(): static
    {
        $this->add([
            "name" => self::RANG,
            'type' => Text::class,
            'options' => [
                'label' => 'Rang',
            ],
            'attributes' => [
                "id" => self::RANG,
                'readonly' => "readonly",
            ],
        ]);

        $this->setInputfilterSpecification(self::RANG, [
            'name' => self::RANG,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);
        return $this;
    }

    const COUT = "cout";
    protected function initCoutInput(): static
    {
        $this->add([
            "name" => self::COUT,
            'type' => Number::class,
            'options' => [
                'label' => "Coût",
            ],
            'attributes' => [
                "id" => self::COUT,
                "min" => 0,
            ],
        ]);

        $this->setInputfilterSpecification(self::COUT, [
            'name' => self::COUT,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
            ],
        ]);
        return $this;
    }
}