<?php


namespace Application\Form\Referentiel;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Referentiel\Fieldset\SourceFieldset;

/**
 * Class ReferentielPromoForm
 * @package Application\Form
 */
class SourceForm extends AbstractEntityForm
{
    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(SourceFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}