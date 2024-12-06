<?php

namespace Application\Service\Notification;

use Application\Entity\Db\FaqCategorieQuestion;
use Application\Service\Misc\CommonEntityService;

/**
 * Class FaqCategorieQuestionService
 * @package Application\Service\FAQ
 */
class FaqCategorieQuestionService extends CommonEntityService
{
    public function getEntityClass(): string
    {
        return FaqCategorieQuestion::class;
    }
    /**
     * @return array
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAll() : array
    {
        return $this->getObjectRepository()->findBy([], ['ordre' => 'ASC']);
    }
}