<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Application\Entity\Traits\Notification\HasFaqQuestionsTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * FaqCategorieQuestion
 */
class FaqCategorieQuestion implements ResourceInterface,
    HasCodeInterface,
    HasLibelleInterface, HasOrderInterface
{
    /**
     *
     */
    const RESOURCE_ID = 'FaqCategorieQuestion';

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
    use HasFaqQuestionsTrait;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initQuestions();
    }



}
