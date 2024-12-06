<?php


namespace Application\Form\Referentiel;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Referentiel\Fieldset\ReferentielPromoFieldset;

/**
 * Class ReferentielPromoForm
 * @package Application\Form
 */
class ReferentielPromoForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ReferentielPromoFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}