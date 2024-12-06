<?php

namespace Application\Form\Stages\Factory;

use Application\Form\Stages\Validator\SessionStageValidator;
use Application\Service\Stage\SessionStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class SessionStageValidatorFactory
 * @package Application\Form\SessionsStages\Factory
 */
class SessionStageValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * ToDo : voir comment gérer les dépendances dans la configuration pour les validators car non fonctionnel
     * @return \Application\Form\Stages\Validator\SessionStageValidator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageValidator
    {
        $validator = new SessionStageValidator($options);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $validator->setObjectManager($entityManager);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService =  $container->get(SessionStageService::class);
        $validator->setSessionStageService($sessionStageService);

        return $validator;
    }
}