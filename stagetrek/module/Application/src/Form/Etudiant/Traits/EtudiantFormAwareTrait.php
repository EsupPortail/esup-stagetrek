<?php


namespace Application\Form\Etudiant\Traits;

use Application\Form\Etudiant\EtudiantForm;
use Application\Form\Etudiant\EtudiantRechercheForm;

/**
 * Traits EtudiantFormAwareTrait
 * @package Application\Form\Etudiant\Traits
 */
trait EtudiantFormAwareTrait
{
    /**
     * @var EtudiantForm|null $etudiantForm
     */
    protected ?EtudiantForm $etudiantForm = null;

    /**
     * @return EtudiantForm
     */
    public function getAddEtudiantForm(): EtudiantForm
    {
        $form = $this->etudiantForm;
        $form->get(EtudiantForm::INPUT_SUBMIT)->setLabel(EtudiantForm::LABEL_SUBMIT_ADD);
        return $form;
    }
    /**
     * @return EtudiantForm
     */
    public function getEditEtudiantForm(): EtudiantForm
    {
        $form = $this->etudiantForm;
        $form->get(EtudiantForm::INPUT_SUBMIT)->setLabel(EtudiantForm::LABEL_SUBMIT_EDIT);
        return $form;
    }

    /**
     * @param EtudiantForm $etudiantForm
     * @return \Application\Form\Etudiant\Traits\EtudiantFormAwareTrait
     */
    public function setEtudiantForm(EtudiantForm $etudiantForm): static
    {
        $this->etudiantForm = $etudiantForm;
        return $this;
    }

    /**
     * @var EtudiantRechercheForm|null $etudiantRechercheForm
     */
    protected ?EtudiantRechercheForm $etudiantRechercheForm = null;

    /**
     * @return EtudiantRechercheForm
     */
    public function getEtudiantRechercheForm(): EtudiantRechercheForm
    {
        return $this->etudiantRechercheForm;
    }

    /**
     * @param EtudiantRechercheForm $etudiantRechercheForm
     * @return \Application\Form\Etudiant\Traits\EtudiantFormAwareTrait
     */
    public function setEtudiantRechercheForm(EtudiantRechercheForm $etudiantRechercheForm): static
    {
        $this->etudiantRechercheForm = $etudiantRechercheForm;
        return $this;
    }



}