<?php

namespace Application\Assertion;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;
use UnicaenAuthentification\Service\UserContext;
use UnicaenPrivilege\Service\AuthorizeService;

//!!! Ne pas le déplacer dans Factory car doit être dans le répertoire Factory pour générer correctement les Assertions
class AbstractAssertionFactory implements AbstractFactoryInterface
{
    const START = 'Assertion';

    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        $parts = explode('\\', $requestedName);
        if (!$parts || $parts[0] !== self::START) {
            return false;
        }
        return $this->isAssertionRequested($requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $className = $this->getAssertionClassName($requestedName);
        $assertion = new $className;
        $this->initAssertion($assertion, $container);

        return $assertion;
    }

    /**
     * Check assertion exists
     *
     * @param string $requestedName
     * @return bool
     */
    protected function isAssertionRequested(string $requestedName): bool
    {
        $parts = explode('\\', $requestedName);
        $isAssertion = count($parts) === 2;

        $className = $this->getAssertionClassName($requestedName);

        return
            $isAssertion &&
            class_exists($className) &&
            is_a($className, \UnicaenPrivilege\Assertion\AbstractAssertion::class, true);
    }

    /**
     * Initialize service
     *
     * @param AbstractAssertion $assertion
     * @param ContainerInterface $container
     * @return AbstractAssertion
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initAssertion(AbstractAssertion $assertion, ContainerInterface $container): AbstractAssertion
    {
        /**
         * @var MvcEvent $mvcEvent
         * @var AuthorizeService $authorizeService
         * @var UserContext $userContextService
         * @var ServiceManager $serviceManager
         * @var EntityManager $entityManager
         * @var ValidatorPluginManager $validatorManager
         */
        $mvcEvent = $container->get('application')->getMvcEvent();
        $authorizeService = $container->get('BjyAuthorize\Service\Authorize');
        $userContextService = $container->get('UnicaenAuthentification\Service\UserContext');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $serviceManager = $container->get(ServiceManager::class);
        $validatorManager = $container->get(ValidatorPluginManager::class);

        $assertion->setMvcEvent($mvcEvent);
        $assertion->setServiceAuthorize($authorizeService);
        $assertion->setServiceUserContext($userContextService);
        $assertion->setObjectManager($entityManager);
        $assertion->setServiceManager($serviceManager);
        $assertion->setValidatorManager($validatorManager);
        return $assertion;
    }

    /**
     * Get assertion class name
     *
     * @param string $requestedName
     * @return string
     */
    protected function getAssertionClassName(string $requestedName): string
    {
        $parts = explode('\\', $requestedName);
        $parts = array_slice($parts, 1);
        $domain = $parts[0];
        return __NAMESPACE__ . sprintf('\\%s%s', $domain, self::START);
    }
}