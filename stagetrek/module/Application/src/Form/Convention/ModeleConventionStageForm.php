<?php
namespace Application\Form\Convention;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Convention\Fieldset\ModeleConventionStageFieldset;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;

class ModeleConventionStageForm extends AbstractEntityForm
{

    use MacroServiceAwareTrait;

    public function getMacros() : string
    {
        return $this->getMacroService()->generateJSON();
    }


    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ModeleConventionStageFieldset::class);
        $this->setEntityFieldset($fieldset);
    }

}
