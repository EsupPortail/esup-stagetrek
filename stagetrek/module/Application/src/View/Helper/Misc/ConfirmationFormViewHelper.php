<?php

namespace Application\View\Helper\Misc;

use Application\Form\Misc\ConfirmationForm;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class ConfirmationFormViewHelper
 * @package Application\View\Helper\Messages
 */
class ConfirmationFormViewHelper extends AbstractHelper
{

    /** @var ConfirmationForm */
    protected $form;

    /**
     * @return ConfirmationForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param ConfirmationForm $form
     */
    public function setForm(ConfirmationForm $form)
    {
        $this->form = $form;
    }

    /**
     * @param ConfirmationForm $form
     */
    public function __invoke(ConfirmationForm $form)
    {
        $this->form = $form;
        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }


    const TEMPLATE_FORM_CONFIRMATION = "layout/templates/form-confirmation";

    public function render()
    {
        if(!$this->form) return "";
        return $this->view->render(self::TEMPLATE_FORM_CONFIRMATION);
    }

}