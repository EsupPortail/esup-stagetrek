<?php


namespace Application\Form\Affectation;


use Application\Entity\Db\AffectationStage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use Application\Provider\Misc\Label;

/**
 * Class AffectationStageForm
 * @package Application\Form\AffectationStage
 */
class AffectationStageForm extends AbstractEntityForm
{
    public function init(): static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(AffectationStageFieldset::class);
        $this->setEntityFieldset($fieldset);
       $this->get(self::INPUT_SUBMIT)->setLabel(Label::render(Label::AFFECTER));
       return $this;
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