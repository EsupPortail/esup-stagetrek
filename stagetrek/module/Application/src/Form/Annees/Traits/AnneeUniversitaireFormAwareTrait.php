<?php

namespace Application\Form\Annees\Traits;

use Application\Form\Annees\AnneeUniversitaireForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait  AnneeUniversitaireFormAwareTrait
{
    /**
     * @var AnneeUniversitaireForm|null $anneeUniversitaireForm;
     */
    protected ?AnneeUniversitaireForm $anneeUniversitaireForm = null;

    /**
     * @return AnneeUniversitaireForm
     */
    public function getAddAnneeUniversitaireForm() : AnneeUniversitaireForm
    {
        $form = $this->anneeUniversitaireForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return AnneeUniversitaireForm
     */
    public function getEditAnneeUniversitaireForm() : AnneeUniversitaireForm
    {
        $form = $this->anneeUniversitaireForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param AnneeUniversitaireForm $anneeUniversitaireForm
     * @return \Application\Form\Annees\Traits\AnneeUniversitaireFormAwareTrait
     */
    public function setAnneeUniversitaireForm(AnneeUniversitaireForm $anneeUniversitaireForm) : static
    {
        $this->anneeUniversitaireForm = $anneeUniversitaireForm;
        return $this;
    }
}