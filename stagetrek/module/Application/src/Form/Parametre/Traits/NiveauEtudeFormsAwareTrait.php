<?php


namespace Application\Form\Parametre\Traits;

use Application\Form\Parametre\NiveauEtudeForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits NiveauEtudeFormsAwareTrait
 * @package Application\Form\Traits
 */
trait NiveauEtudeFormsAwareTrait
{
    /**
     * @var NiveauEtudeForm|null $niveauEtudeForm
     */
    protected ?NiveauEtudeForm $niveauEtudeForm = null;

    /**
     * @return NiveauEtudeForm
     */
    public function getAddNiveauEtudeForm(): NiveauEtudeForm
    {
        $form = $this->niveauEtudeForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }
    /**
     * @return NiveauEtudeForm
     */
    public function getEditNiveauEtudeForm(): NiveauEtudeForm
    {
        $form = $this->niveauEtudeForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param NiveauEtudeForm $niveauEtudeForm
     * @return NiveauEtudeFormsAwareTrait
     */
    public function setNiveauEtudeForm(NiveauEtudeForm $niveauEtudeForm) : static
    {
        $this->niveauEtudeForm = $niveauEtudeForm;
        return $this;
    }
}