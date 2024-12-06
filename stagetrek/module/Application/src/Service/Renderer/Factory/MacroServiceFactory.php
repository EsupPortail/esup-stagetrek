<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Parametre\ParametreService;
use Application\Service\Renderer\AdresseRendererService;
use Application\Service\Renderer\ContactRendererService;
use Application\Service\Renderer\ParametreRendererService;
use Application\Service\Renderer\PdfRendererService;
use Application\Service\Renderer\DateRendererService;
use Application\Service\Renderer\MacroService;
use Application\Service\Renderer\UrlService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

class MacroServiceFactory
{
    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): MacroService
    {

        $serviceProvider = new MacroService();

        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $serviceProvider->setObjectManager($entityManager);

        /** @var DateRendererService $dateRenderService */
        $dateRenderService = $container->get(ServiceManager::class)->get(DateRendererService::class);
        $serviceProvider->setDateRendererService($dateRenderService);

        /** @var AdresseRendererService $adresseRendererService */
        $adresseRendererService = $container->get(ServiceManager::class)->get(AdresseRendererService::class);
        $serviceProvider->setAdresseRendererService($adresseRendererService);

        /** @var UrlService $urlService */
        $urlService = $container->get(ServiceManager::class)->get(UrlService::class);
        $serviceProvider->setUrlService($urlService);

        /** @var ContactRendererService $contactRendererService */
        $contactRendererService = $container->get(ServiceManager::class)->get(ContactRendererService::class);
        $serviceProvider->setContactRendererService($contactRendererService);

        /** @var PdfRendererService $conventionRendererService */
        $conventionRendererService = $container->get(ServiceManager::class)->get(PdfRendererService::class);
        $serviceProvider->setPdfRendererService($conventionRendererService);

        /** @var ParametreRendererService $parametreRendererService */
        $parametreRendererService = $container->get(ServiceManager::class)->get(ParametreRendererService::class);
        $serviceProvider->setParametreRendererService($parametreRendererService);

        return $serviceProvider;
    }
}