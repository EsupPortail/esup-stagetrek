<?php


namespace Application\Form\Affectation;


use Application\Entity\Db\AffectationStage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;

/**
 * Class AffectationStageForm
 * @package Application\Form\AffectationStage
 */
class AffectationStageForm extends AbstractEntityForm
{

    const LABEL_SUBMIT_AFFECTER = "Affecter";

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(AffectationStageFieldset::class);
        $this->setEntityFieldset($fieldset);
       $this->get(self::INPUT_SUBMIT)->setLabel(self::LABEL_SUBMIT_AFFECTER);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function setAffectationStage(AffectationStage $affectationStage): static
    {
        /** @var AffectationStageFieldset $fieldset */
        $fieldset = $this->getEntityFieldset();
        $fieldset->setAffectationStage($affectationStage);
        return $this;
    }

}