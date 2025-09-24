<?php


namespace Application\Form\Contrainte\Fieldset;

use Application\Entity\Traits\Contraintes\HasContrainteCursusEtudiantTrait;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Laminas\Form\Element\Number;
use Laminas\I18n\Validator\IsInt;

/**
 * Class ContrainteCursusEtudiantFieldset
 * @package Application\Form\ContraintesCursus\Fieldset
 */
class ContrainteCursusEtudiantFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    /** Placeholder des inputs */
    public function init() : static
    {
        $this->initIdInput();
        $this->initEquivalenceInput();
        return $this;
    }

    const NB_STAGE_VALIDE_EQUIVALENCE = "nbEquivalences";
    private function initEquivalenceInput() : void
    {
            $this->add([
            "name" => self::NB_STAGE_VALIDE_EQUIVALENCE,
            "type" => Number::class,
            "options" => [
                "label" => "Nombre de stage(s) validé(s) par équivalence",
            ],
            "attributes" => [
                "id" => self::NB_STAGE_VALIDE_EQUIVALENCE,
    //                "min" => 0,
            ],
        ]);
            $this->setInputfilterSpecification(self::NB_STAGE_VALIDE_EQUIVALENCE,[
                "name" => self::NB_STAGE_VALIDE_EQUIVALENCE,
                'required' => false,
                'validators' => [
                    [
                        'name' => IsInt::class,
                    ],
                ],
            ]);
    }

    use HasContrainteCursusEtudiantTrait;
}