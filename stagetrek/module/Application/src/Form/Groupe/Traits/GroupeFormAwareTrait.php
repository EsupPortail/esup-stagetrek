<?php


namespace Application\Form\Groupe\Traits;

use Application\Form\Groupe\GroupeForm;
use Application\Form\Groupe\GroupeRechercheForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits GroupeFormAwareTrait
 * @package Application\Form\Groupe\Traits
 */
trait GroupeFormAwareTrait
{
    /**
     * @var GroupeForm|null $groupeForm
     */
    protected ?GroupeForm $groupeForm = null;

    /** @var GroupeRechercheForm|null $groupeRechercheForm */
    protected ?GroupeRechercheForm $groupeRechercheForm =null;

    /**
     * @return GroupeForm
     */
    public function getAddGroupeForm(): GroupeForm
    {
        $form = $this->groupeForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return GroupeForm
     */
    public function getEditGroupeForm(): GroupeForm
    {
        $form = $this->groupeForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param GroupeForm $groupeForm
     * @return GroupeFormAwareTrait
     */
    public function setGroupeForm(GroupeForm $groupeForm) : static
    {
        $this->groupeForm = $groupeForm;
        return $this;
    }

    /**
     * @return GroupeRechercheForm
     */
    public function getGroupeRechercheForm(): GroupeRechercheForm
    {
        return $this->groupeRechercheForm;
    }

    /**
     * @param GroupeRechercheForm $groupeRechercheForm
     * @return GroupeFormAwareTrait
     */
    public function setGroupeRechercheForm(GroupeRechercheForm $groupeRechercheForm) : static
    {
        $this->groupeRechercheForm = $groupeRechercheForm;
        return $this;
    }
}