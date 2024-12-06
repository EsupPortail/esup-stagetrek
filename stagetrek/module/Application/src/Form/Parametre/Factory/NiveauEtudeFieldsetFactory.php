<?php


namespace Application\Form\Parametre\Factory;

use Application\Entity\Db\NiveauEtude;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\Parametre\Fieldset\NiveauEtudeFieldset;
use Application\Form\Parametre\Hydrator\NiveauEtudeHydrator;
use Application\Service\Parametre\NiveauEtudeService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class NiveauEtudeFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class NiveauEtudeFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : NiveauEtudeFieldset
    {
        $fieldset = new NiveauEtudeFieldset('niveauEtude');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var NiveauEtudeHydrator $niveauEtudeHydrator */
        $niveauEtudeHydrator = $container->get('HydratorManager')->get(NiveauEtudeHydrator::class);
        $fieldset->setHydrator($niveauEtudeHydrator);
        $fieldset->setObject(new NiveauEtude());

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($container->get(ServiceManager::class)->get(NiveauEtudeService::class));
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}