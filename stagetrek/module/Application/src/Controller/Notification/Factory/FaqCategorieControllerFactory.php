<?php

namespace Application\Controller\Notification\Factory;

use Application\Controller\Notification\FaqCategorieController;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Notification\FaqCategorieQuestionForm;
use Application\Service\Notification\FaqCategorieQuestionService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class Stag
 * @package Application\Controller\Stages\Factory
 */
class FaqCategorieControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FaqCategorieController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqCategorieController
    {
        $controller = new FaqCategorieController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var FaqCategorieQuestionService $faqCategorieQuestionService */
        $faqCategorieQuestionService = $container->get(ServiceManager::class)->get(FaqCategorieQuestionService::class);
        $controller->setFaqCategorieQuestionService($faqCategorieQuestionService);

        /** @var FaqCategorieQuestionForm $faqCategorieQuestionForm */
        $faqCategorieQuestionForm = $container->get(FormElementManager::class)->get(FaqCategorieQuestionForm::class);
        $controller->setFaqCategorieQuestionForm($faqCategorieQuestionForm);

        /** @var ConfirmationForm $confirmationForm */
        $confirmationForm = $container->get(FormElementManager::class)->get(ConfirmationForm::class);
        $controller->setConfirmationForm($confirmationForm);

        return $controller;
    }
}