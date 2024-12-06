<?php


namespace Application\Form\Notification\Traits;

use Application\Form\Notification\MessageInfoForm;

/**
 * Traits MessageInfoFormAwareTrait
 * @package Application\Form\Message\Traits
 */
trait MessageInfoFormAwareTrait
{

    /**
     * @var MessageInfoForm|null $messageInfoForm
     **/
    protected ?MessageInfoForm $messageInfoForm = null;

    /**
     * /**
     * @return MessageInfoForm
     */
    public function getAddMessageInfoForm(): MessageInfoForm
    {
        $form = $this->messageInfoForm;
        $form->get($form::SUBMIT)->setLabel("<i class='fas fa-save'></i> Ajouter");
        return $form;
    }

    /**
     * @return MessageInfoForm
     */
    public function getEditMessageInfoForm(): MessageInfoForm
    {
        $form = $this->messageInfoForm;
        $form->get($form::SUBMIT)->setLabel("<i class='fas fa-save'></i> Modifier");
        $form->get($form::SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param MessageInfoForm $messageInfoForm
     */
    public function setMessageInfoForm(MessageInfoForm $messageInfoForm): void
    {
        $this->messageInfoForm = $messageInfoForm;
    }


}