<?php


namespace Application\Form\Parametre;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Parametre\Fieldset\ParametreFieldset;

/**
 * Class ParametreForm
 * @package Application\Form
 */
class ParametreForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ParametreFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}