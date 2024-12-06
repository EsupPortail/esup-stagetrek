<?php


namespace Application\Form\Groupe;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Abstrait\Interfaces\AbstractFormConstantesInterface;
use Application\Form\Groupe\Fieldset\GroupeFieldset;

/**
 * Class GroupeForm
 * @package Application\Form
 */
class GroupeForm extends AbstractEntityForm implements AbstractFormConstantesInterface
{

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(GroupeFieldset::class);
        $this->setEntityFieldset($fieldset);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function setAnneeUniversitaire(AnneeUniversitaire $annee) : static
    {
        /** @var GroupeFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setAnneeUniversitaire($annee);
        return $this;
    }
}
