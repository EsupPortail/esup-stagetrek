<?php


namespace Application\Form\Misc\Factory;


use Application\Form\Abstrait\Element\AbstractSelectPicker;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

class SelectPickerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractSelectPicker
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): AbstractSelectPicker
    {
        /** @var AbstractSelectPicker $selectPicker */
        $selectPicker = new $requestedName();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $selectPicker->setObjectManager($entityManager);

        return $selectPicker;
    }
}