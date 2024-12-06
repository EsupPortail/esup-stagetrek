<?php

namespace Fichier\Controller\Factory;

use Fichier\Controller\FichierController;
use Fichier\Form\Upload\UploadForm;
use Fichier\Service\Fichier\FichierService;
use Fichier\Service\Nature\NatureService;
use Interop\Container\ContainerInterface;

class FichierControllerFactory {

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): FichierController
    {
        /**
         * @var NatureService $natureService
         * @var FichierService $fichierService
         */
        $natureService = $container->get(NatureService::class);
        $fichierService = $container->get(FichierService::class);
//        $s3Service = $container->get(S3Service::class);

        /**
         * @var UploadForm $uploadForm
         */
        $uploadForm = $container->get('FormElementManager')->get(UploadForm::class);

        $controller = new FichierController();
        $controller->setNatureService($natureService);
        $controller->setFichierService($fichierService);
        $controller->setUploadForm($uploadForm);
        return $controller;
    }
}