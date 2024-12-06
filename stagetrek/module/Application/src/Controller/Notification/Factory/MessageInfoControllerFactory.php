<?php

namespace Application\Controller\Notification\Factory;

use Application\Controller\Notification\MessageInfoController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Notification\MessageInfoForm;
use Application\Service\Notification\MessageInfoService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class MessageInfoControllerFactory
 * @package Application\Controller\Stages\Factory
 */
class MessageInfoControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MessageInfoController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MessageInfoController
    {
        $controller = new MessageInfoController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var MessageInfoService $messageInfoService */
        $messageInfoService = $container->get(ServiceManager::class)->get(MessageInfoService::class);
        $controller->setMessageInfoService($messageInfoService);

        /** @var MessageInfoForm $messageInfoForm */
        $messageInfoForm = $container->get(FormElementManager::class)->get(MessageInfoForm::class);
        $controller->setMessageInfoForm($messageInfoForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }
}