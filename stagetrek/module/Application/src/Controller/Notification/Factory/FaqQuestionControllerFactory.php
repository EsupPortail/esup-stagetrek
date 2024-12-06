<?php

namespace Application\Controller\Notification\Factory;

use Application\Controller\Notification\FaqQuestionController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Notification\FaqQuestionForm;
use Application\Service\Notification\FaqService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenAuthentification\Service\UserContext;

/**
 * Class Stag
 * @package Application\Controller\Stages\Factory
 */
class FaqQuestionControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FaqQuestionController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqQuestionController
    {
        $controller = new FaqQuestionController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var FaqService $faqQuestionService */
        $faqQuestionService = $container->get(ServiceManager::class)->get(FaqService::class);
        $controller->setFaqQuestionService($faqQuestionService);

        /** @var FaqQuestionForm $faqQuestionForm */
        $faqQuestionForm = $container->get(FormElementManager::class)->get(FaqQuestionForm::class);
        $controller->setFaqQuestionForm($faqQuestionForm);

        /** @var UserContext $userContext */
        $userContext = $container->get('UnicaenAuthentification\Service\UserContext');
        $controller->setServiceUserContext($userContext);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }
}