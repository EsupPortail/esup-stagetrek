<?php

namespace Application\Entity\Traits\Notification;

use Application\Entity\Db\Faq;

trait HasFaqQuestionTrait
{
    /** @var FAQ|null  $question*/
    protected ?FAQ $question;

    public function getQuestion(): ?Faq
    {
        return $this->question;
    }

    public function setQuestion(?Faq $faq): void
    {
        $this->question = $faq;
    }

    /**
     * @return bool
     */
    public function hasQuestion(): bool
    {
        return isset($this->question);
    }

}