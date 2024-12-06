<?php


namespace Application\Form\Annees;


use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Annees\Fieldset\AnneeUniversitaireFieldset;
class AnneeUniversitaireForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(AnneeUniversitaireFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}