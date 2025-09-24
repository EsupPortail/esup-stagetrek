<?php


namespace Application\Form\Etudiant;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Etudiant\Fieldset\DisponibiliteFieldset;

/**
 * Class DisponibiliteForm
 * @package Application\Form\Disponibilite
 */
class DisponibiliteForm extends AbstractEntityForm
{
    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(DisponibiliteFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
