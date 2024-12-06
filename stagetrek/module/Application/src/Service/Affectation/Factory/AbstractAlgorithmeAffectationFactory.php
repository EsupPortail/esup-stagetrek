<?php

namespace Application\Service\Affectation\Factory;

use Application\Service\Affectation\Algorithmes\AbstractAlgorithmeAffectation;
use Application\Service\Affectation\Algorithmes\AlgorithmeAffectationInterface;
use Application\Service\Parametre\ParametreService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class AbstractAlgorithmeAffectationFactory implements AbstractFactoryInterface
{
    /**
     * Can the factory create an instance for the service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName)
            && in_array(AlgorithmeAffectationInterface::class, class_implements($requestedName));
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractAlgorithmeAffectation
    {
        $algo = new $requestedName;
        $this->initAlgo($algo, $container);

        return $algo;
    }

    /**
     * Initialize service
     *
     * @param AbstractAlgorithmeAffectation $algo
     * @param ContainerInterface $container
     * @return AbstractAlgorithmeAffectation
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initAlgo(AbstractAlgorithmeAffectation $algo, ContainerInterface $container): AbstractAlgorithmeAffectation
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $algo->setObjectManager($entityManager);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $algo->setParametreService($parametreService);
        return $algo;
    }

}