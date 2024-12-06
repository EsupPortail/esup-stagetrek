<?php


namespace Application\Form\Contrainte;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Contrainte\Fieldset\ContrainteCursusFieldset;
/**
 * Class ContrainteCursusForm
 * @package Application\Form\ContraintesCursus
 */
class ContrainteCursusForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ContrainteCursusFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}
