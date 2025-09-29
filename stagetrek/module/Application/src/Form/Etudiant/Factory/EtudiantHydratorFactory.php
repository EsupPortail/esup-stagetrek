<?php

namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\Hydrator\EtudiantHydrator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class EtudiantHydratorFactory
 * @package Application\Form\Etudiant\Factory
 */
class EtudiantHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EtudiantHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantHydrator
    {
        $hydrator = new EtudiantHydrator();
        $hydrator->setTagService($container->get(TagService::class));
        return $hydrator;
    }

}