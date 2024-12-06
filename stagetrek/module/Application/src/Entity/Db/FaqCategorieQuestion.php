<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Application\Entity\Traits\Notification\HasFaqQuestionsTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * FaqCategorieQuestion
 */
class FaqCategorieQuestion implements ResourceInterface, LibelleEntityInterface, OrderEntityInterface
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

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
    use HasFaqQuestionsTrait;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initQuestions();
    }



}
