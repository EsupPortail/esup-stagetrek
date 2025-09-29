<?php

namespace Application\Form\Contacts\Traits;

use Application\Form\Contacts\ContactStageForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait ContactStageFormAwareTrait
{
    /**
     * @var ContactStageForm|null $contactStageForm ;
     */
    protected ?ContactStageForm $contactStageForm = null;

    /**
     * @return ContactStageForm
     *
     */
    public function getAddContactStageForm(): ContactStageForm
    {
        $form = $this->contactStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return ContactStageForm
     *
     */
    public function getEditContactStageForm(): ContactStageForm
    {
        $form = $this->contactStageForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        $form->setModeEdition();
        return $form;
    }

    /**
     * @return ContactStageForm
     *
     */
    public function getContactStageForm() : ContactStageForm
    {
        return $this->contactStageForm;
    }

    /**
     * @param ContactStageForm $contactStageForm
     * @return \Application\Form\Contacts\Traits\ContactStageFormAwareTrait
     */
    public function setContactStageForm(ContactStageForm $contactStageForm) : static
    {
        $this->contactStageForm = $contactStageForm;
        return $this;
    }
}