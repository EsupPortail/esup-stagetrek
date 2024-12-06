<?php

namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\Hydrator\EtudiantHydrator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EtudiantHydrator
    {
        return new EtudiantHydrator();
    }

}