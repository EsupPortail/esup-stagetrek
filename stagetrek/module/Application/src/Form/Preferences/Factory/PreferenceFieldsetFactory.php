<?php


namespace Application\Form\Preferences\Factory;

use Application\Entity\Db\Preference;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;
use Application\Form\Preferences\Hydrator\PreferenceHydrator;
use Application\Service\Parametre\ParametreService;
use Application\Service\Preference\PreferenceService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferenceFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class PreferenceFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PreferenceFieldset
    {
        $fieldset = new PreferenceFieldset('preference');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /**
         * @var ParametreService $parametreService
         */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $fieldset->setParametreService($parametreService);

        /** @var PreferenceService $preferenceService */
        $preferenceService = $container->get(ServiceManager::class)->get(PreferenceService::class);
        $fieldset->setPreferenceService($preferenceService);

        /** @var PreferenceHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(PreferenceHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Preference());

        return $fieldset;
    }
}