<?php

namespace Application\Service\Notification\Traits;

use Application\Service\Notification\FaqCategorieQuestionService;

Trait FAQCategorieQuestionServiceAwareTrait
{

    /** @var FaqCategorieQuestionService $faqCategorieQuestionService */
    protected FaqCategorieQuestionService $faqCategorieQuestionService;

    /**
     * @return FaqCategorieQuestionService
     */
    public function getFaqCategorieQuestionService(): FaqCategorieQuestionService
    {
        return $this->faqCategorieQuestionService;
    }

    /**
     * @param FaqCategorieQuestionService $faqCategorieQuestionService
     */
    public function setFaqCategorieQuestionService(FaqCategorieQuestionService $faqCategorieQuestionService): void
    {
        $this->faqCategorieQuestionService = $faqCategorieQuestionService;
    }
}