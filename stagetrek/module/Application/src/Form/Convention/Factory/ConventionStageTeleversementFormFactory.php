<?php


namespace Application\Form\Convention\Factory;


use Application\Entity\Db\ConventionStage;
use Application\Form\Convention\ConventionStageTeleversementForm;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\HelperPluginManager;

class ConventionStageTeleversementFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ConventionStageTeleversementForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConventionStageTeleversementForm
    {
        $form = new ConventionStageTeleversementForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $form->setHydrator($hydrator);
        $form->setObject(new ConventionStage());

        return $form;
    }

}
