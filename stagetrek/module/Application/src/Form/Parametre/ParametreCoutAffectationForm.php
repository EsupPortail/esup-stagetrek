<?php


namespace Application\Form\Parametre;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Parametre\Fieldset\ParametreCoutAffectationFieldset;

/**
 * Class ParametreCoutAffectationForm
 * @package Application\Form
 */
class ParametreCoutAffectationForm extends AbstractEntityForm
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ParametreCoutAffectationFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}