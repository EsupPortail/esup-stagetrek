<?php

namespace Application\Entity\Traits\Notification;

use Application\Entity\Db\Faq;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasFaqQuestionsTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $questions;

    /**
     * @return void
     */
    protected function initQuestions(): void
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * @param FAQ $question
     */
    public function addQuestion(FAQ $question) : void
    {
        if(!$this->questions->contains($question)){
            $this->questions->add($question);
        }
    }

    /**
     * Remove question.
     *
     * @param FAQ $question
     * @return boolean
     */
    public function removeQuestion(FAQ $question) : bool
    {
        return $this->questions->removeElement($question);
    }

    /**
     * Get questions.
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions() : Collection
    {
        return $this->questions;
    }
}