<?php


namespace Application\Form\Stages;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Abstrait\Interfaces\AbstractFormConstantesInterface;
use Application\Form\Stages\Fieldset\SessionStageFieldset;

/**
 * Class SessionStageForm
 * @package Application\Form\SessionsStages
 */
class SessionStageForm extends AbstractEntityForm implements AbstractFormConstantesInterface
{

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(SessionStageFieldset::class);
        $this->setEntityFieldset($fieldset);
    }

    // Fonction qui permet de fixer l'année universitaire selectionnée / le groupe


    public function setAnneeUniversitaire(AnneeUniversitaire $annee) : static
    {
        /** @var SessionStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setAnneeUniversitaire($annee);
        return $this;
    }

    public function setGroupe(Groupe $groupe) : static
    {
        /** @var SessionStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setGroupe($groupe);
        return $this;
    }
}