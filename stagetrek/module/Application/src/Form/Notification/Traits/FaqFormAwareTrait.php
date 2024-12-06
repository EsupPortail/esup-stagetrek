<?php


namespace Application\Form\Notification\Traits;

use Application\Form\Notification\FaqCategorieQuestionForm;
use Application\Form\Notification\FaqQuestionForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits FaqFormAwareTrait
 * @package Application\Form\Traits
 */
trait FaqFormAwareTrait
{

    /**
     * @var FaqQuestionForm|null $faqQuestionForm
     **/
    protected ?FaqQuestionForm $faqQuestionForm = null;

    /**
     * @var FaqCategorieQuestionForm|null $faqCategorieQuestionForm
     */
    protected ?FaqCategorieQuestionForm $faqCategorieQuestionForm = null;


    /**
     * @return FaqQuestionForm
     */
    public function getAddFaqQuestionForm(): FaqQuestionForm
    {
        $form = $this->faqQuestionForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return FaqQuestionForm
     */
    public function getEditFaqQuestionForm(): FaqQuestionForm
    {
        $form = $this->faqQuestionForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param FaqQuestionForm $faqQuestionForm
     * @return \Application\Form\Notification\Traits\FaqFormAwareTrait
     */
    public function setFaqQuestionForm(FaqQuestionForm $faqQuestionForm): static
    {
        $this->faqQuestionForm = $faqQuestionForm;
        return $this;
    }


    /**
     * @return FaqCategorieQuestionForm
     */
    public function getAddFaqCategorieQuestionForm(): FaqCategorieQuestionForm
    {
        $form = $this->faqCategorieQuestionForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::AJOUTER));
        return $form;
    }

    /**
     * @return FaqCategorieQuestionForm
     */
    public function getEditFaqCategorieQuestionForm(): FaqCategorieQuestionForm
    {
        $form = $this->faqCategorieQuestionForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param FaqCategorieQuestionForm $faqCategorieQuestionForm
     * @return \Application\Form\Notification\Traits\FaqFormAwareTrait
     */
    public function setFaqCategorieQuestionForm(FaqCategorieQuestionForm $faqCategorieQuestionForm): static
    {
        $this->faqCategorieQuestionForm = $faqCategorieQuestionForm;
        return $this;
    }

}