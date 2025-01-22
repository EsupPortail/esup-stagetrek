<?php


namespace Application\Form\Convention\Factory;


use Application\Entity\Db\ModeleConventionStage;
use Application\Form\Convention\Hydrator\ModeleConventionStageHydrator;
use Application\Form\Convention\ModeleConventionStageForm;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use UnicaenRenderer\Service\TemplateEngineManager\TemplateEngineManager;

class ModeleConventionStageFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModeleConventionStageForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ModeleConventionStageForm
    {
        $form = new ModeleConventionStageForm();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $form->setObjectManager($entityManager);

        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $form->setMacroService($macroService);

        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $container->get('ViewHelperManager');
        $form->setViewHelperManager($viewHelperManager);

        /** @var ModeleConventionStageHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ModeleConventionStageHydrator::class);
        $form->setHydrator($hydrator);
        $form->setObject(new ModeleConventionStage());

        /** @var TemplateEngineManager $templateEngineManager */
        $templateEngineManager = $container->get(TemplateEngineManager::class);
        $form->setTemplateEngineManager($templateEngineManager);

        return $form;
    }

}
