<?php

namespace Application\Controller\Misc\Interfaces;

use Application\Controller\Misc\Traits\ActionsMessagesToolsTrait;
use Application\Form\Misc\Traits\ConfirmationFormAwareTrait;
use Application\Misc\RouterToolsTrait;
use Application\Misc\UserToolsTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * abstract class AbstractActionController
 * @package Application\Controller\Interfaces
 * @author Thibaut Vallée <thibaut.vallée at unicaen.fr>
 * Classe abstraite permettant de gérer les fonctions commune aux multiples controlleur
 *
 * @method isAllowed($resource, $privilege = null)
 * @method FlashMessenger flashMessenger()
 */
abstract class AbstractActionController extends \Laminas\Mvc\Controller\AbstractActionController implements
    ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use ConfirmationFormAwareTrait;
    use ActionsMessagesToolsTrait;
    use RouterToolsTrait;
    use UserToolsTrait;
}