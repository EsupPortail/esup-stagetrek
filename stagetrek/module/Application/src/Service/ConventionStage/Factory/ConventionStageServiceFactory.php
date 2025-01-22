<?php

namespace Application\Service\ConventionStage\Factory;

use Application\Service\ConventionStage\ConventionStageFileNameFormatter;
use Application\Service\ConventionStage\ConventionStageService;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Exception;
use Fichier\Filter\FileName\FileNameFormatterInterface;
use Fichier\Service\Fichier\FichierService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Renderer\PhpRenderer;
use UnicaenPdf\Exporter\PdfExporter;
use UnicaenRenderer\Service\Rendu\RenduService;

/**
 * Class ConventionStageServiceFactory
 * @package Application\Service\ConventionStage
 */
class ConventionStageServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ConventionStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConventionStageService
    {
        $service = new ConventionStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        try{
            /** @var FichierService $fileServcie */
            $fileServcie = $container->get(ServiceManager::class)->get(FichierService::class);
            $service->setFileService($fileServcie);

            /** @var FileNameFormatterInterface $fileNameFormatter */
            $fileNameFormatter = $container->get(ServiceManager::class)->get(ConventionStageFileNameFormatter::class);
            $fileServcie->setFileNameFormatter($fileNameFormatter);
        }
        catch (Exception $e){
            //Permet de ne pas avoir d'erreur en l'abscence de fileService (si non configuré)
        }
        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $service->setMacroService($macroService);
        $pdfExporter = new PdfExporter();
        $pdfExporter->setRenderer($container->get(PhpRenderer::class));
        $service->setPdfExporter($pdfExporter);

        /** @var RenduService $renduService */
        $renduService = $container->get(ServiceManager::class)->get(RenduService::class);
        $service->setRenduService($renduService);


        return $service;
    }
}