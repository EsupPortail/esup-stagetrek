<?php


namespace Application\Form\Referentiel\Traits;

use Application\Form\Referentiel\ReferentielPromoForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ReferentielPromoFormAwareTrait
 * @package Application\Form\Traits
 */
trait ReferentielPromoFormAwareTrait
{

    /**
     * @var ReferentielPromoForm|null $referentielPromoForm
     */
    protected ?ReferentielPromoForm $referentielPromoForm = null;

    /**
     * @return ReferentielPromoForm
     */
    public function getAddReferentielPromoForm(): ReferentielPromoForm
    {
        $form = $this->referentielPromoForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return ReferentielPromoForm
     */
    public function getEditReferentielPromoForm(): ReferentielPromoForm
    {
        $form = $this->referentielPromoForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ReferentielPromoForm $referentielPromoForm
     * @return ReferentielPromoFormAwareTrait
     */
    public function setReferentielPromoForm(ReferentielPromoForm $referentielPromoForm) : static
    {
        $this->referentielPromoForm = $referentielPromoForm;
        return $this;
    }
}