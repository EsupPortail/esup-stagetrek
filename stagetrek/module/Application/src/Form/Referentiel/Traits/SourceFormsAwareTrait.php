<?php


namespace Application\Form\Referentiel\Traits;

use Application\Form\Referentiel\SourceForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ReferentielPromoFormsAwareTrait
 * @package Application\Form\Traits
 */
trait SourceFormsAwareTrait
{

    /**
     * @var \Application\Form\Referentiel\SourceForm|null $sourceForm
     */
    protected ?SourceForm $sourceForm = null;

    /**
     * @return SourceForm
     */
    public function getAddSourceForm(): SourceForm
    {
        $form = $this->sourceForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return SourceForm
     */
    public function getSourceEditForm(): SourceForm
    {
        $form = $this->sourceForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param SourceForm $sourceForm
     * @return \Application\Form\Referentiel\Traits\SourceFormsAwareTrait
     */
    public function setSourceForm(SourceForm $sourceForm) : static
    {
        $this->sourceForm = $sourceForm;
        return $this;
    }
}