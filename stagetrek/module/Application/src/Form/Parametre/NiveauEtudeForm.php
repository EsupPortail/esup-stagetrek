<?php


namespace Application\Form\Parametre;

use Application\Entity\Db\NiveauEtude;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Parametre\Fieldset\NiveauEtudeFieldset;

/**
 * Class NiveauEtudeForm
 * @package Application\Form
 */
class NiveauEtudeForm extends AbstractEntityForm
{

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(NiveauEtudeFieldset::class);
        $this->setEntityFieldset($fieldset);
    }

    public function fixerNiveauEtude(NiveauEtude $niveauEtude): static
    {
        /** @var NiveauEtudeFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->fixerNiveauEtude($niveauEtude);
        return $this;
    }
}