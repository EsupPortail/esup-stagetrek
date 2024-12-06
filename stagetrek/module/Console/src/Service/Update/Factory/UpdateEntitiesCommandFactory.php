<?php

namespace Console\Service\Update\Factory;

use Console\Service\Update\Interfaces\UpdateEntityCommandInterface;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

class UpdateEntitiesCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @return \Console\Service\Update\UpdateEntitiesCommand
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UpdateEntityCommandInterface
    {
        $command = new $requestedName();
        if(!$command instanceof UpdateEntityCommandInterface){
            throw new Exception("La comande demandée n'est pas un service de mise à jours des données");
        }
        $serviceManager = $container->get(ServiceManager::class);
        $command->setServiceManager($serviceManager);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $command->setObjectManager($entityManager);

        return $command;
    }
}