<?php

namespace Application\Entity\Traits\Notification;


use Application\Entity\Db\FaqCategorieQuestion;

trait HasFaqCategorieQuestionTrait
{
    /** @var FaqCategorieQuestion|null  $faqCategorieQuestion*/
    protected ?FaqCategorieQuestion $faqCategorieQuestion;

    public function getFaqCategorieQuestion(): ?FaqCategorieQuestion
    {
        return $this->faqCategorieQuestion;
    }

    public function setFaqCategorieQuestion(?FaqCategorieQuestion $faqCategorieQuestion): void
    {
        $this->faqCategorieQuestion = $faqCategorieQuestion;
    }

    /**
     * @return bool
     */
    public function hasFaqCategorieQuestion(): bool
    {
        return isset($this->faqCategorieQuestion);
    }

}