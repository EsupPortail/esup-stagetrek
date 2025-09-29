<?php

namespace Application\Form\Contacts\Traits;

use Application\Form\Contacts\ContactTerrainForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait ContactTerrainFormAwareTrait
{
    /**
     * @var ContactTerrainForm|null $contactTerrainForm ;
     */
    protected ?ContactTerrainForm $contactTerrainForm = null;

    /**
     * @return ContactTerrainForm
     *
     */
    public function getAddContactTerrainForm(): ContactTerrainForm
    {
        $form = $this->contactTerrainForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::AJOUTER, Icone::AJOUTER));
        return $form;
    }

    /**
     * @return ContactTerrainForm
     *
     */
    public function getEditContactTerrainForm(): ContactTerrainForm
    {
        $form = $this->contactTerrainForm;
        $form->get($form::SUBMIT)->setLabel(Label::render(Label::MODIFIER, Icone::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        $form->setModeEdition();
        return $form;
    }

    /**
     * @param ContactTerrainForm $contactTerrainForm
     * @return \Application\Form\Contacts\Traits\ContactTerrainFormAwareTrait
     */
    public function setContactTerrainForm(ContactTerrainForm $contactTerrainForm) : static
    {
        $this->contactTerrainForm = $contactTerrainForm;
        return $this;
    }

}