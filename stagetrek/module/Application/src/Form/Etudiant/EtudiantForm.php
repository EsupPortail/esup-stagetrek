<?php

namespace Application\Form\Etudiant;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
/**
 * Class EtudiantForm
 * @package Application\Form\Etudiant
 */
class EtudiantForm extends AbstractEntityForm
{
    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(EtudiantFieldset::class);
        $this->setEntityFieldset($fieldset);
    }
}