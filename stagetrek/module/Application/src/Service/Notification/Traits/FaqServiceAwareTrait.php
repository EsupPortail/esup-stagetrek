<?php

namespace Application\Service\Notification\Traits;

use Application\Service\Notification\FaqService;

/**
 * Traits FaqServiceAwareTrait
 * @package Application\Service\FAQ
 */
Trait FaqServiceAwareTrait
{
    /** @var FaqService|null $faqService */
    protected ?FaqService $faqQuestionService = null;

    /**
     * @return FaqService
     */
    public function getFaqQuestionService(): FaqService
    {
        return $this->faqQuestionService;
    }

    /**
     * @param FaqService $faqQuestionService
     * @return \Application\Service\Notification\Traits\FaqServiceAwareTrait
     */
    public function setFaqQuestionService(FaqService $faqQuestionService): static
    {
        $this->faqQuestionService = $faqQuestionService;
        return $this;
    }
}