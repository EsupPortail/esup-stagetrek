<?php


namespace Application\Form\Adresse\Factory;

use Application\Entity\Db\Adresse;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AdresseFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class AdresseFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AdresseFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AdresseFieldset
    {
        $fieldset = new AdresseFieldset(AdresseFieldset::FIELDSET_NAME);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Adresse());

        return $fieldset;
    }
}