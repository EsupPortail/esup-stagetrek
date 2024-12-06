<?php


namespace Application\Form\Affectation\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\DescriptionInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Laminas\Filter\StringTrim;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\StringLength;

/**
 * Class AffectationStageFieldset
 * @package Application\Form\AffectationStage\Fieldset;
 */
class ProcedureAffectationFieldset  extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;
    use DescriptionInputAwareTrait;

    public function init(): void
    {
        $this->initIdInput();
        $this->initCodeInput();
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initDescriptionInput();
    }

    private function initDescriptionInput() : void
    {
        $this->add([
            'name' => self::DESCRIPTION,
            'type' => Textarea::class,
            'options' => [
                'label' => "Description",
            ],
            'attributes' => [
                'id' => self::DESCRIPTION,
                "rows" => 10,
            ],
        ]);

        $this->setInputfilterSpecification(self::DESCRIPTION, [
            "name" => self::DESCRIPTION,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                    ],
                ],
            ],
        ]);
    }
}