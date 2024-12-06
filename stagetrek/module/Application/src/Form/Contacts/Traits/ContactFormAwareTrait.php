<?php

namespace Application\Form\Contacts\Traits;

use Application\Form\Contacts\ContactForm;
use Application\Form\Contacts\ContactRechercheForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

trait ContactFormAwareTrait
{
    /**
     * @var ContactForm|null $contactForm ;
     */
    protected ?ContactForm $contactForm = null;

    /** @var ContactRechercheForm|null $contactRechercheForm */
    protected ?ContactRechercheForm $contactRechercheForm = null;

    /**
     * @return ContactForm
     *
     */
    public function getAddContactForm() : ContactForm
    {
        $form = $this->contactForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return ContactForm
     */
    public function getEditContactForm() : ContactForm
    {
        $form = $this->contactForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ContactForm $contactForm
     * @return \Application\Form\Contacts\Traits\ContactFormAwareTrait
     */
    public function setContactForm(ContactForm $contactForm) : static
    {
        $this->contactForm = $contactForm;
        return $this;
    }

    /**
     * @return \Application\Form\Contacts\ContactRechercheForm
     */
    public function getContactRechercheForm(): ContactRechercheForm
    {
        return $this->contactRechercheForm;
    }

    /**
     * @param \Application\Form\Contacts\ContactRechercheForm $contactRechercheForm
     */
    public function setContactRechercheForm(ContactRechercheForm $contactRechercheForm): void
    {
        $this->contactRechercheForm = $contactRechercheForm;
    }
}