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
        $serviceProvider = new ConventionStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        try{
            /** @var FichierService $fileServcie */
            $fileServcie = $container->get(ServiceManager::class)->get(FichierService::class);
            $serviceProvider->setFileService($fileServcie);

            /** @var FileNameFormatterInterface $fileNameFormatter */
            $fileNameFormatter = $container->get(ServiceManager::class)->get(ConventionStageFileNameFormatter::class);
            $fileServcie->setFileNameFormatter($fileNameFormatter);
        }
        catch (Exception $e){
            //Permet de ne pas avoir d'erreur en l'abscence de fileService (si non configuré)
        }
        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $serviceProvider->setMacroService($macroService);
        $pdfExporter = new PdfExporter();
        $pdfExporter->setRenderer($container->get(PhpRenderer::class));
        $serviceProvider->setPdfExporter($pdfExporter);


        return $serviceProvider;
    }
}