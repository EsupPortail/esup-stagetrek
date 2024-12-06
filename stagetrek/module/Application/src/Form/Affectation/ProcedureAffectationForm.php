<?php


namespace Application\Form\Affectation;


use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Affectation\Fieldset\ProcedureAffectationFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

/**
 * Class AffectationStageForm
 * @package Application\Form\AffectationStage
 */
class ProcedureAffectationForm extends AbstractEntityForm implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    public function init(): void
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ProcedureAffectationFieldset::class);
        $this->setEntityFieldset($fieldset);
       $this->get(self::INPUT_SUBMIT)->setLabel(self::LABEL_SUBMIT_EDIT);
    }

}